<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generate($id)
    {
        $order = Order::with(['products', 'user', 'payments'])->findOrFail($id);

        $subtotal = 0;
        foreach ($order->products as $product) {
            $product->line_total = $product->price * $product->pivot->quantity;
            $subtotal += $product->line_total;
        }

        $total = $subtotal;
        $pdf = Pdf::loadView('invoices.template', [
            'order' => $order,
            'subtotal' => $subtotal,
            'total' => $total,
            'payments' => $order->payments,
            'company' => [
                'name' => 'Tlece Bangladesh Ltd.',
                'address' => 'House-82/B, Road-4/6, Block-B, Section-12, Pallabi, Mirpur, Dhaka, Bangladesh.',
                'phone' => '+880-19-66-22-4474',
                'email' => 'hello@tlece.com',
                'logo' => public_path('images/logo.png')
            ]
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $order->id . '.pdf');
    }


}
