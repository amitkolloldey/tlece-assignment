<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Order') }}
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

                    <h3 class="text-lg font-semibold mb-4">Create New Order</h3>

                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                        <div class="mb-4">
                            <label for="product_search" class="block text-gray-700">Search Products</label>
                            <input type="text" id="product_search"
                                class="w-full px-3 py-2 border border-gray-300  focus:ring focus:ring-blue-200"
                                placeholder="Search for products..." onkeyup="searchProducts()">
                            <div id="product_list"
                                class="mt-2 max-h-60 overflow-auto border border-gray-300 bg-white absolute z-10 w-full hidden">
                            </div>
                        </div>

                        <div class="mb-4" id="selected_products"></div>

                        <div class="mt-6" id="payment_section" style="display: none;">
                            <h4 class="text-md font-semibold">Payment Methods</h4>
                            <button type="button" onclick="addPaymentMethod()"
                                class="bg-green-500 text-white px-2 py-1 rounded my-2">+ Add Payment</button>
                            <div id="payment_methods"></div>
                            <p id="payment_error" class="text-red-500 hidden">Total payments should not exceed total
                                amount.</p>
                        </div>
                        <div class="my-4">
                            <h4 class="text-md font-semibold">Total Amount: $<span id="total_amount">0.00</span></h4>
                        </div>


                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" disabled>Create
                            Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedProducts = [];
        let paymentMethods = [];

        function searchProducts() {
            const query = document.getElementById("product_search").value;
            if (query.length >= 2) {
                fetch(`/products/search/result?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        const productList = document.getElementById("product_list");
                        productList.innerHTML = "";
                        if (data.length > 0) {
                            data.forEach(product => {
                                const div = document.createElement('div');
                                div.classList.add('cursor-pointer', 'p-2', 'hover:bg-gray-200');
                                div.innerHTML = `${product.name} - $${product.price}`;
                                div.onclick = () => selectProduct(product);
                                productList.appendChild(div);
                            });
                            productList.classList.remove('hidden');
                        } else {
                            productList.classList.add('hidden');
                        }
                    });
            } else {
                document.getElementById("product_list").classList.add('hidden');
            }
        }

        function selectProduct(product) {
            if (!selectedProducts.find(p => p.id === product.id)) {
                selectedProducts.push({
                    ...product,
                    quantity: 1
                });
                renderSelectedProducts();
                document.getElementById("product_search").value = '';
                document.getElementById("product_list").classList.add('hidden');
            }
        }

        function renderSelectedProducts() {
            const container = document.getElementById("selected_products");
            container.innerHTML = '';
            let totalAmount = 0;

            selectedProducts.forEach((product, index) => {
                const div = document.createElement('div');
                div.classList.add('flex', 'items-center', 'mb-2');
                div.innerHTML = `
            <span class="w-1/2">${product.name} - $${product.price}</span>
            <input type="number" name="products[${product.id}][quantity]" value="${product.quantity}" min="1" class="w-1/4 px-3 py-2 border border-gray-300 focus:ring focus:ring-blue-200" onchange="updateQuantity(${index}, this.value)">
            <button type="button" onclick="removeProduct(${index})" class="text-red-500 ml-2">Remove</button>
        `;
                container.appendChild(div);
                totalAmount += product.price * product.quantity;
            });


            document.getElementById('payment_section').style.display = totalAmount > 0 ? 'block' : 'none';

            updateTotal();
        }

        function updateQuantity(index, value) {
            selectedProducts[index].quantity = parseInt(value) || 1;
            renderSelectedProducts();
        }

        function updateTotal() {
            let totalAmount = selectedProducts.reduce((sum, product) => sum + product.price * product.quantity, 0);

            document.getElementById('total_amount').textContent = totalAmount.toFixed(2);

            validatePayments();
        }


        function removeProduct(index) {
            selectedProducts.splice(index, 1);
            renderSelectedProducts();
        }

        function addPaymentMethod() {
            const container = document.getElementById("payment_methods");
            const index = paymentMethods.length;
            paymentMethods.push({
                method: '',
                amount: 0
            });
            const div = document.createElement('div');
            div.classList.add('flex', 'items-center', 'mb-2');
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
            let totalAmount = selectedProducts.reduce((sum, product) => sum + product.price * product.quantity, 0);
            let paymentTotal = Array.from(document.querySelectorAll('.payment-amount')).reduce((sum, input) => sum + (
                parseFloat(input.value) || 0), 0);

            const paymentErrorElement = document.getElementById('payment_error');
            paymentErrorElement.classList.toggle('hidden', paymentTotal === totalAmount);


            const submitButton = document.querySelector('button[type="submit"]');
            if (paymentTotal === totalAmount) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }
    </script>
</x-app-layout>
