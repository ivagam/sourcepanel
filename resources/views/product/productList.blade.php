@extends('layout.layout')
@php
    $title = 'Product Grid';
    $subTitle = 'Product Grid';
    $script = '<script>

        let table = new DataTable("#dataTable");

        $(".remove-item-btn").on("click", function() {
            $(this).closest("tr").addClass("d-none")
        });
    </script>';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="card basic-data-table">    
        <div class="card-header">
            <h5 class="card-title mb-0">Media List</h5>
        </div>
        <div class="card-body">
            
            <table class="table bordered-table sm-table mb-0" id="dataTable" data-page-length='10'>
                <thead>
                    <tr>
                        <th class="text-center">S.L</th>
                        <th class="text-center">Product Name</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Product Price</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $key => $product)
                        <tr>
                            <td>
                                    <div class="d-flex align-items-center gap-10">                                    
                                        {{ $key + 1 }}
                                    </div>
                            </td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->category_name }}</td>
                            <td>{{ $product->product_price }}</td>
                            <td>{{ $product->description }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('editProduct', $product->product_id) }}">
                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>
                                    <form action="{{ route('deleteProduct', $product->product_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No product found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>                                  

        </div>
    </div>

@endsection

<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>
