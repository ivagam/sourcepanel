@extends('layout.layout')

@php
    $title = 'Product List A';
    $subTitle = 'Product List A';
    $script = '';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<style>
    .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #0d6efd;
        border: 2px solid #bbb;
        background-color: #f9f9f9;
    }

    #select-all.form-check-input {
        accent-color: #198754;
    }

    .form-check-input:hover {
        border-color: #666;
    }
</style>
<div class="card basic-data-table">
    <div class="card-header">
        {{-- Search & Filter --}}
        <div class="row align-items-center mt-3">
            <div class="col-md-7">
                <form method="GET" action="{{ route('scrapeList') }}">
                    <div class="d-flex gap-2">
                        {{-- Dropdown Filter --}}
                        <select name="category_filter" class="form-select">
                            <option value="">All Categories</option>
                            <option value="1" {{ request('category_filter') == '1' ? 'selected' : '' }}>Watches</option>
                            <option value="113" {{ request('category_filter') == '113' ? 'selected' : '' }}>Others</option>
                        </select>

                        {{-- Text Search --}}
                        <input type="text" name="search" value="{{ request('searchscrape') }}" class="form-control" placeholder="Search">

                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{ route('scrapeList') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12 d-flex justify-content-end">
                {{ $scrape->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <div class="card-body">
        {{-- ✅ Bulk Action Bar --}}
        <div id="bulk-action-bar" class="mb-3">
            <button id="bulk-update-sku" class="btn btn-warning">Set Update Related Prodcut</button>
        </div>

        <div class="table-responsive">
            <table class="table bordered-table mb-0" style="min-width: 1000px;">
                <thead>
                    <tr>
                        {{-- ✅ Master Checkbox --}}
                        <th class="text-center" style="width:5%;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th class="text-center text-nowrap" style="width: 10%;">Action</th>
                        <th class="text-center text-nowrap" style="width: 10%;">Image</th>
                        <th class="text-center" style="width: 10%;">Product Name</th>
                        <th class="text-center" style="width: 10%;">Product Value</th>
                        <th class="text-center" style="width: 10%;">Category</th>
                        <th class="text-center" style="width: 10%;">Product Price</th>
                        <th class="text-center" style="width: 10%;">Numbers</th>
                        <th class="text-center" style="width: 15%;">Description</th>
                        <th class="text-center" style="width: 15%;">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($scrape as $product)
                        @php
                            $media = $product->images->sortBy('serial_no')->first();
                            $mediaUrl = $media ? env('SOURCE_PANEL_IMAGE_URL') . $media->file_path : null;
                            $ext = $media ? strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION)) : null;
                            $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                        @endphp

                        <tr>
                            {{-- ✅ Row Checkbox --}}
                            <td class="text-center align-middle">
                                <input type="checkbox" class="row-checkbox form-check-input" value="{{ $product->scrape_product_id }}">
                            </td>

                            {{-- ✅ Action --}}
                            <td class="text-center align-middle">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <a href="{{ route('editScrape', $product->scrape_product_id) }}">
                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>

                                    <a href="{{ route('duplicateScrapeProduct', $product->scrape_product_id) }}">
                                        <button type="button" class="bg-primary-focus text-primary-600 bg-hover-primary-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="carbon:copy" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>

                                    <form action="{{ route('deleteScrapeProduct', $product->scrape_product_id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>

                            {{-- ✅ Image / Video --}}
                            <td class="text-center align-middle">
                                @if($media && $mediaUrl)
                                    @if(in_array($ext, $videoExtensions))
                                        <video width="80" height="80" muted autoplay loop playsinline style="object-fit:cover;border-radius:5px;display:block;">
                                            <source src="{{ $mediaUrl }}" type="video/{{ $ext }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src="{{ $mediaUrl }}" alt="{{ $product->product_name }}" style="width:80px;height:80px;object-fit:cover;border-radius:5px;">
                                    @endif
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>

                            {{-- ✅ Remaining Columns --}}
                            <td class="align-middle">{{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::title($product->product_name), 60) }}</td>
                            <td class="align-middle">{{ $product->sku }}</td>
                            <td class="align-middle">{{ $product->category_name }}</td>
                            <td class="align-middle">
                                @if($product->product_price && $product->product_price != 0)
                                    USD{{ number_format($product->product_price) }}
                                @endif
                            </td>

                            <td class="align-middle">
                                @if($product->purchase_value && $product->purchase_value != 0)
                                    USD{{ number_format($product->purchase_value) }}<br>
                                    <p class="text-muted">{{ $product->purchase_code }}</p>
                                @endif
                            </td>
                            <td class="align-middle">{{ \Illuminate\Support\Str::limit($product->description, 60) }}</td>
                            <td class="align-middle">{{ $product->note }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">No product found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-end">
            {{ $scrape->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Auto fade alert
    setTimeout(function () {
        $(".alert").fadeOut("slow");
    }, 3000);

    // Delete single product
    $(document).on('click', '.remove-item-btn', function (event) {
        event.preventDefault();
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

    // Select all checkboxes
    $('#select-all').on('change', function () {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkBar();
    });

    // Toggle bulk action bar
    $(document).on('change', '.row-checkbox', function () {
        $('#select-all').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
        toggleBulkBar();
    });

    function toggleBulkBar() {
        if ($('.row-checkbox:checked').length > 0) {
            $('#bulk-action-bar').show();
        } else {
            $('#bulk-action-bar').hide();
        }
    }

    // Bulk Update SKU
    $('#bulk-update-sku').on('click', function (e) {
        e.preventDefault();

        let selectedIds = $('.row-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one product.');
            return;
        }

        if (confirm('Update SKU for ' + selectedIds.length + ' products?')) {
            $.ajax({
                url: "{{ route('bulkUpdateScrapeSku') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    ids: selectedIds
                },
                success: function (response) {
                    alert(response.message);
                    location.reload();
                },
                error: function () {
                    alert('Failed to update SKU.');
                }
            });
        }
    });
</script>
@endsection