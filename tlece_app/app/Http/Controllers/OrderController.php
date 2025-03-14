<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = Order::with('user')->get();

            return DataTables::of($orders)
                ->addColumn('created_at_human', function ($order) {
                    return $order->created_at->diffForHumans();
                })
                ->addColumn('user_name', function ($order) {
                    return $order->user->name;
                })
                ->addColumn('action', function ($order) {
                    return '
                <a href="' . route('orders.show', $order->id) . '" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-blue-500">
                    View
                </a>
                <a href="' . route('orders.edit', $order->id) . '" class="inline-flex items-center px-3 py-2 text-sm text-white bg-gray-500 ">
                    Edit
                </a>
                <a href="' . route('invoices.generate', $order->id) . '" target="_blank" class="inline-flex items-center px-3 py-2 text-sm text-white bg-gray-800 ">
                    Invoice
                </a>
                <form action="' . route('orders.destroy', $order->id) . '" method="POST" style="display:inline;">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="inline-flex items-center px-3 py-2 text-sm text-white bg-red-600" onclick="return confirm(\'Are you sure you want to delete this order?\')">
                        Delete
                    </button>
                </form>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('orders.index');
    }



    public function create()
    {
        return view('orders.create', ['products' => Product::all()]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.quantity' => 'required|numeric|min:1',
            'payments' => 'required|array',
            'payments.*.method' => 'required|string|in:cash,bkash,nagad,card',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        $order = new Order();
        $order->user_id = auth()->id();
        $order->total_amount = 0;
        $order->save();

        $totalAmount = 0;
        foreach ($request->input('products') as $productId => $product) {
            $quantity = $product['quantity'];
            $product = Product::find($productId);

            if ($product) {
                $totalAmount += $product->price * $quantity;
                $order->products()->attach($productId, ['quantity' => $quantity]);
            }
        }

        $order->total_amount = $totalAmount;
        $order->save();

        // Store Payments
        $totalPaid = 0;
        foreach ($request->input('payments') as $payment) {
            $totalPaid += $payment['amount'];
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $payment['method'],
                'amount' => $payment['amount']
            ]);
        }

        if ($totalPaid != $totalAmount) {
            return redirect()->back()->with('error', 'Total payment amount does not match order total.');
        }

        return redirect()->route('orders.index')->with('success', 'Order created with multiple payment modes!');
    }



    public function show($id)
    {
        $order = Order::with(['user', 'products', 'payments'])->findOrFail($id);

        $order->total_amount = $order->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });

        return view('orders.show', compact('order'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = Order::with(['products', 'payments'])->findOrFail($id);
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.quantity' => 'required|numeric|min:1',
            'payments' => 'required|array',
            'payments.*.method' => 'required|string|in:cash,bkash,nagad,card',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);

        $order = Order::findOrFail($id);

        // Update product quantities
        foreach ($request->input('products') as $productId => $data) {
            $order->products()->updateExistingPivot($productId, ['quantity' => $data['quantity']]);
        }

        // Recalculate total amount
        $order->total_amount = $order->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });
        $order->save();

        // Remove old payments and add new ones
        $order->payments()->delete();
        $totalPaid = 0;
        foreach ($request->input('payments') as $payment) {
            $totalPaid += $payment['amount'];
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $payment['method'],
                'amount' => $payment['amount']
            ]);
        }

        if ($totalPaid != $order->total_amount) {
            return redirect()->back()->with('error', 'Total payment amount does not match order total.');
        }

        return redirect()->route('orders.show', $id)->with('success', 'Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Delete related payments
        $order->payments()->delete();

        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }

    /**
     * Remove a product from an order.
     */
    public function removeProduct(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $productId = $request->input('product_id');

        $order->products()->detach($productId);

        // Recalculate total amount after product removal
        $order->total_amount = $order->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });
        $order->save();

        return redirect()->route('orders.edit', $orderId)->with('success', 'Product removed from the order.');
    }


}
