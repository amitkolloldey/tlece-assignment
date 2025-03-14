<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with('user')
                ->select(['id', 'name', 'sku', 'price', 'created_at', 'updated_at', 'user_id']);

            return DataTables::of($products)
                ->addColumn('action', function ($row) {
                    return '
                    <a href="' . route('products.show', $row->id) . '" class="inline-flex items-center px-3 py-2 text-sm text-white bg-blue-500">
                        View
                    </a>
                    <a href="' . route('products.edit', $row->id) . '" class="inline-flex items-center px-3 py-2 text-sm text-white bg-gray-500">
                        Edit
                    </a>
                    <form action="' . route('products.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm text-white bg-red-600">
                            Delete
                        </button>
                    </form>
                ';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->user ? $row->user->name : 'N/A';
                })
                ->addColumn('created_at_human', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('products.index');
    }


    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|unique:products',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        auth()->user()->products()->create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }



    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'sku' => 'required|unique:products,sku,' . $id,
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }


    public function search(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'like', "%$query%")
            ->orWhere('sku', 'like', "%$query%")
            ->get(['id', 'name', 'sku', 'price']);

        return response()->json($products);
    }
}
