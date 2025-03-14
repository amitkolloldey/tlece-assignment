<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }}
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
                    <h3 class="text-lg font-semibold mb-4">Product Details</h3>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-semibold">Product Name</label>
                        <p class="text-gray-900">{{ $product->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="sku" class="block text-gray-700 font-semibold">SKU</label>
                        <p class="text-gray-900">{{ $product->sku }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 font-semibold">Product Description</label>
                        <p class="text-gray-900">{{ $product->description }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block text-gray-700 font-semibold">Price</label>
                        <p class="text-gray-900">{{ number_format($product->price, 2) }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="block text-gray-700 font-semibold">Quantity</label>
                        <p class="text-gray-900">{{ $product->quantity }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="created_by" class="block text-gray-700 font-semibold">Created By</label>
                        <p class="text-gray-900">{{ $product->user ? $product->user->name : 'N/A' }}</p>
                    </div>

                    <div class="mb-4">
                        <label for="created_at" class="block text-gray-700 font-semibold">Created At</label>
                        <p class="text-gray-900">{{ $product->created_at->diffForHumans() }}</p>
                    </div>

                    <a href="{{ route('products.index') }}"
                        class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white bg-blue-500 hover:bg-blue-700">
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
