@extends('layout.layout')
@php
    $title = 'Product Grid';
    $subTitle = 'Product Grid';
    $script = '<script>

        let table = new DataTable("#dataTable");       
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
            
            <div class="table-responsive">
        <table class="table bordered-table mb-0" id="dataTable" style="min-width: 1000px;">
                <thead>
                    <tr>
                        <th class="text-center text-nowrap" style="width: 10%;">Action</th>
                        <th class="text-center" style="width: 20%;">Product Name</th>
                        <th class="text-center" style="width: 20%;">Category</th>
                        <th class="text-center" style="width: 15%;">Product Price</th>
                        <th class="text-center" style="width: 30%;">Description</th>                        
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $key => $product)
                        <tr>
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
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->category_name }}</td>
                            <td>{{ $product->product_price }}</td>
                            <td>{{ $product->description }}</td>                            
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
    </div>

@endsection

<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);

    $(document).on('click', '.remove-item-btn', function() {
    let form = $(this).closest('form');
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                form.closest('tr').fadeOut();
            },
            error: function() {
                alert('Failed to delete. Please try again.');
            }
        });
    }
});
</script>
