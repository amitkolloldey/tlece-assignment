<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 text-sm text-green-600 bg-green-100 p-2 rounded">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="mb-4 text-sm text-red-600 bg-red-100 p-2 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold mb-4">Order #{{ $order->id }} Details</h3>

                    <div class="mb-4">
                        <strong>Customer:</strong> {{ $order->user->name }}
                    </div>
                    
                    <div class="mb-4">
                        <strong>Created At:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}
                    </div>

                    <h4 class="text-lg font-semibold mt-6 mb-4">Products</h4>
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 border">Product Name</th>
                                <th class="px-4 py-2 border">SKU</th>
                                <th class="px-4 py-2 border">Quantity</th>
                                <th class="px-4 py-2 border">Price</th>
                                <th class="px-4 py-2 border">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->products as $product)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $product->name }}</td>
                                    <td class="px-4 py-2 border">{{ $product->sku }}</td>
                                    <td class="px-4 py-2 border">{{ $product->pivot->quantity }}</td>
                                    <td class="px-4 py-2 border">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-4 py-2 border">${{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}
                    </div>

                    <h4 class="text-lg font-semibold mt-6 mb-4">Payment Details</h4>
                    <table class="min-w-full bg-white border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 border">Payment Method</th>
                                <th class="px-4 py-2 border">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->payments as $payment)
                                <tr>
                                    <td class="px-4 py-2 border">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-4 py-2 border">${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        <strong>Total Payments:</strong> ${{ number_format($order->payments->sum('amount'), 2) }}
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('orders.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
