@extends('layout.layout')

@php
    $title = 'Product List B';
    $subTitle = 'Product List B';
    $script = '';
@endphp

@section('content')
    


@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card basic-data-table">
  <div class="card-header">
    {{-- First row: Search bar only --}}
    <div class="row align-items-center mt-3">
        <div class="col-md-7">
            <form method="GET" action="{{ route('productListB') }}">
                <div class="d-flex gap-2">
                    {{-- Dropdown Filter --}}
                    <select name="category_filter" class="form-select">
                        <option value="">All Categories</option>
                        <option value="1" {{ request('category_filter') == '1' ? 'selected' : '' }}>Watches</option>
                        <option value="113" {{ request('category_filter') == '113' ? 'selected' : '' }}>Others</option>
                    </select>

                    {{-- Text Search --}}
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search">

                    <button class="btn btn-primary" type="submit">Search</button>
                    <a href="{{ route('productListB') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12 d-flex justify-content-end">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" style="min-width: 1000px;">
                <thead>
                    <tr>
                        {{-- ✅ Keep original order --}}
                        <th class="text-center text-nowrap" style="width: 10%;">Action</th>
                        <th class="text-center text-nowrap" style="width: 10%;">Image</th>
                        <th class="text-center" style="width: 15%;">Product Name</th>
                        <th class="text-center" style="width: 15%;">SKU</th>
                        <th class="text-center" style="width: 15%;">Category</th>
                        <th class="text-center" style="width: 15%;">Product Price</th>
                        <th class="text-center" style="width: 20%;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $media = $product->images->sortBy('serial_no')->first();
                            $mediaUrl = $media ? env('SOURCE_PANEL_IMAGE_URL') . $media->file_path : null;
                            $ext = $media ? strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION)) : null;
                            $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                        @endphp

                        <tr>
                            {{-- ✅ Action First --}}
                            <td class="text-center align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('editProduct', $product->product_id) }}">
                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>

                                    <a href="{{ route('duplicateProduct', $product->product_id) }}">
                                        <button type="button" class="bg-primary-focus text-primary-600 bg-hover-primary-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="carbon:copy" class="menu-icon"></iconify-icon></button>
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

                            {{-- ✅ Image or Video Second --}}
                            <td class="text-center align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>
                                @if($media && $mediaUrl)
                                    @if(in_array($ext, $videoExtensions))
                                        <video width="80" height="80" muted autoplay loop playsinline
                                            style="object-fit: cover; border-radius: 5px; display: block;">
                                            <source src="{{ $mediaUrl }}" type="video/{{ $ext }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src="{{ $mediaUrl }}" alt="{{ $product->product_name }}"
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px;">
                                    @endif
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- ✅ Remaining Columns --}}
                            <td class="align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>{{ \Illuminate\Support\Str::title($product->product_name) }}</td>
                            <td class="align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>{{ $product->sku }}</td>
                            <td class="align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>{{ $product->category_name }}</td>
                            <td class="align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>${{ number_format($product->product_price, 2) }}</td>
                            <td class="align-middle" @if($product->is_updated) style="background-color: #e4f7e4ff !important;" @endif>{{ \Illuminate\Support\Str::limit($product->description, 60) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No product found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
            <div class="mt-3 text-end">
                {{ $products->appends(request()->query())->links() }}
            </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Auto-fade alert + AJAX delete --}}
<script>
    setTimeout(function () {
        $(".alert").fadeOut("slow");
    }, 3000);

    $(document).on('click', '.remove-item-btn', function () {
        let form = $(this).closest('form');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function () {
                    form.closest('tr').fadeOut();
                },
                error: function () {
                    alert('Failed to delete. Please try again.');
                }
            });
        }
    });
</script>
@endsection