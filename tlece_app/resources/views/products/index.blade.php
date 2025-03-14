@section('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Product List</h3>

                        <a href="{{ route('products.create') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4">
                            Create Product
                        </a>
                    </div>

                    <table id="productsTable" class="min-w-full bg-white ">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2 border">ID</th>
                                <th class="px-4 py-2 border">Name</th>
                                <th class="px-4 py-2 border">SKU</th>
                                <th class="px-4 py-2 border">Price</th>
                                <th class="px-4 py-2 border">Created By</th>
                                <th class="px-4 py-2 border">Created At</th>
                                <th class="px-4 py-2 border">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#productsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('products.index') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'sku',
                            name: 'sku'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'created_at_human',
                            name: 'created_at_human'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            });
        </script>
    @endsection
</x-app-layout>
