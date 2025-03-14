<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Product') }}
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
                    <h3 class="text-lg font-semibold mb-4">Add New Product</h3>

                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">Product Name</label>
                            <input type="text" name="name" id="name"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-gray-700">Product Description</label>
                            <textarea name="description" id="description"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200" required>{{ old('description') }}</textarea>
                            @error('name')
                                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="sku" class="block text-gray-700">SKU</label>
                            <input type="text" name="sku" id="sku"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                                value="{{ old('sku') }}" required>
                            @error('sku')
                                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-gray-700">Price</label>
                            <input type="number" name="price" id="price" step="0.01"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                                value="{{ old('price') }}" required>
                            @error('price')
                                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="quantity" class="block text-gray-700">Quantity</label>
                            <input type="number" name="quantity" id="quantity"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200"
                                value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4">
                            Save Product
                        </button>
                    </form>

                    <div id="successMessage" class="hidden mt-4 text-green-600"></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
