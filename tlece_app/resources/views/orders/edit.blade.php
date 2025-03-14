<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Order') }}
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

                    <h3 class="text-lg font-semibold mb-4">Edit Order #{{ $order->id }}</h3>

                    <form method="POST" action="{{ route('orders.update', $order->id) }}">
                        @csrf
                        @method('PUT')
 
                        <h4 class="text-lg font-semibold mt-6 mb-4">Products</h4>
                        <table class="min-w-full bg-white border">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 border">Product Name</th>
                                    <th class="px-4 py-2 border">SKU</th>
                                    <th class="px-4 py-2 border">Quantity</th>
                                    <th class="px-4 py-2 border">Price</th>
                                    <th class="px-4 py-2 border">Total</th>
                                    <th class="px-4 py-2 border">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $product)
                                    <tr data-product-id="{{ $product->id }}">
                                        <td class="px-4 py-2 border">{{ $product->name }}</td>
                                        <td class="px-4 py-2 border">{{ $product->sku }}</td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" name="products[{{ $product->id }}][quantity]"
                                                value="{{ $product->pivot->quantity }}" min="1"
                                                class="quantity-input w-16 px-3 py-2 border border-gray-300 focus:ring focus:ring-blue-200"
                                                data-price="{{ $product->price }}" oninput="updateTotalAmount()">
                                        </td>
                                        <td class="px-4 py-2 border">${{ number_format($product->price, 2) }}</td>
                                        <td class="px-4 py-2 border product-total">
                                            ${{ number_format($product->price * $product->pivot->quantity, 2) }}
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <button type="button" class="text-red-500"
                                                onclick="removeProduct({{ $product->id }})">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-6">
                            <strong>Total Amount:</strong> $<span id="totalAmount">{{ number_format($order->total_amount, 2) }}</span>
                        </div>
 
                        <div class="mt-6" id="payment_section" style="display: none;">
                            <h4 class="text-md font-semibold">Payment Methods</h4>
                            <button type="button" onclick="addPaymentMethod()" class="bg-green-500 text-white px-2 py-1 rounded my-2">+ Add Payment</button>
                            <div id="payment_methods">
                                @foreach ($order->payments as $index => $payment)
                                    <div class="flex items-center mb-2">
                                        <select name="payments[{{ $index }}][method]" class="px-3 py-2 border border-gray-300 mr-2">
                                            <option value="cash" {{ $payment->payment_method === 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="bkash" {{ $payment->payment_method === 'bkash' ? 'selected' : '' }}>bKash</option>
                                            <option value="nagad" {{ $payment->payment_method === 'nagad' ? 'selected' : '' }}>Nagad</option>
                                            <option value="card" {{ $payment->payment_method === 'card' ? 'selected' : '' }}>Card</option>
                                        </select>
                                        <input type="number" name="payments[{{ $index }}][amount]" value="{{ $payment->amount }}" min="0" class="px-3 py-2 border border-gray-300 w-1/4 payment-amount" oninput="validatePayments()">
                                        <button type="button" onclick="this.parentElement.remove(); validatePayments();" class="text-red-500 ml-2">Remove</button>
                                    </div>
                                @endforeach
                            </div>
                            <p id="payment_error" class="text-red-500 hidden">Total payments should not exceed total amount.</p>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" id="updateOrderBtn" disabled>
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script> 
        function updateTotalAmount() {
            let totalAmount = 0;
            document.querySelectorAll('.quantity-input').forEach(function (input) {
                const price = parseFloat(input.getAttribute('data-price'));
                const quantity = parseInt(input.value) || 0;
                const productTotal = price * quantity;
                totalAmount += productTotal;
                input.closest('tr').querySelector('.product-total').textContent = `$${productTotal.toFixed(2)}`;
            });
            document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
            document.getElementById('payment_section').style.display = totalAmount > 0 ? 'block' : 'none';
            validatePayments();
        }
 
        function addPaymentMethod() {
            const container = document.getElementById("payment_methods");
            const index = container.children.length;
            const div = document.createElement("div");
            div.classList.add("flex", "items-center", "mb-2");
            div.innerHTML = `
                <select name="payments[${index}][method]" class="px-3 py-2 border border-gray-300 mr-2">
                    <option value="cash">Cash</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                    <option value="card">Card</option>
                </select>
                <input type="number" name="payments[${index}][amount]" min="0" class="px-3 py-2 border border-gray-300 w-1/4 payment-amount" oninput="validatePayments()">
                <button type="button" onclick="this.parentElement.remove(); validatePayments();" class="text-red-500 ml-2">Remove</button>
            `;
            container.appendChild(div);
        }
 
        function validatePayments() {
            let totalAmount = parseFloat(document.getElementById('totalAmount').textContent);
            let paymentTotal = Array.from(document.querySelectorAll('.payment-amount')).reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            document.getElementById('payment_error').classList.toggle('hidden', paymentTotal <= totalAmount);
            document.getElementById('updateOrderBtn').disabled = paymentTotal !== totalAmount;
        }
 
        function removeProduct(productId) { 
            const productRow = document.querySelector(`tr[data-product-id="${productId}"]`);
            if (productRow) {
                productRow.remove();
                updateTotalAmount(); 
 
                fetch(`/orders/remove-product/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Product removed successfully.");
                    } else {
                        console.log("Failed to remove product.");
                    }
                })
                .catch(error => {
                    console.log("Error removing product:", error);
                });
            }
        }

        // Add event listeners for quantity changes and initialize the total amount
        document.querySelectorAll('.quantity-input').forEach(input => input.addEventListener('input', updateTotalAmount));
        updateTotalAmount();  // Initial call to display the correct total on load
    </script>
</x-app-layout>
