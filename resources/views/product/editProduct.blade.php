@extends('layout.layout')
@php
    $title = 'Edit Product';
    $subTitle = 'Edit Product';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<style>
    .image-box {
        border: 2px solid transparent;
        border-radius: 5px;
        cursor: pointer;
    }
    .image-box.selected {
        border: 2px solid #0d6efd;
    }
    .image-box img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
    }
</style>

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Product</h5>
                </div>
                <div class="card-body">
                    <form class="row gy-3 needs-validation" method="POST" action="{{ route('updateProduct', $product->product_id) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label">Product Name<span class="text-danger-600">*</span></label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name', $product->product_name) }}" required>
                            @error('product_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price<span class="text-danger-600">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control @error('product_price') is-invalid @enderror" placeholder="Enter Product Price" value="{{ old('product_price', $product->product_price) }}" required>
                            @error('product_price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Category<span class="text-danger-600">*</span></label>
                            <select name="category" id="categorySelect" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Domains</label>
                            <select name="domains[]" class="form-control select2 @error('domains') is-invalid @enderror" multiple required>
                                @php
                                    $selectedDomains = old('domains', explode(',', $product->domains));
                                @endphp
                                @foreach ($domains as $domain)
                                    <option value="{{ $domain->domain_id }}" {{ in_array($domain->domain_id, $selectedDomains) ? 'selected' : '' }}>
                                        {{ $domain->domain_name }}
                                    </option>
                                @endforeach
                            </select>
                           
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Product Description</label>
                            <textarea name="description" class="form-control texteditor @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror">{{ old('meta_keywords', $product->meta_keywords) }}</textarea>
                            @error('meta_keywords')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#mediaModal">
                                Add Images
                            </button>
                        </div>

                        <div class="col-md-12 mt-3" id="selectedImagePreview" style="display: none;">
                            <div class="d-flex flex-wrap gap-2 mb-2" id="previewContainer"></div>
                        </div>

                        <div id="selectedImageInputs"></div>

                        <div class="col-md-12">
                            <button class="btn btn-primary-600" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Media Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="modal-body" id="mediaScrollContainer" style="max-height: 280px; overflow-y: auto;">
                <div class="row" id="mediaLibrary">
                    @foreach($media as $image)
                        <div class="col-md-2 mb-3">
                            <div class="image-box" data-id="{{ $image->media_id }}">
                                <img src="../../public/{{ $image->file_path }}" alt="Image" class="img-fluid">
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary" id="confirmSelection">Add Image</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script>
    let selectedMediaIds = new Set();
    let mediaPage = 1;
    let isLoading = false;
    let allMediaLoaded = false;

    function bindImageEvents() {
        $('.image-box').off('click').on('click', function () {
            const id = $(this).data('id').toString();
            $(this).toggleClass('selected');
            selectedMediaIds.has(id) ? selectedMediaIds.delete(id) : selectedMediaIds.add(id);
        });
    }

    function renderSelectedImages() {
        const preview = $('#previewContainer');
        const inputs = $('#selectedImageInputs');
        const wrapper = $('#selectedImagePreview');

        preview.empty();
        inputs.empty();
        wrapper.toggle(selectedMediaIds.size > 0);

        let index = 0;
        selectedMediaIds.forEach(id => {
            const img = $(`.image-box[data-id="${id}"] img`);
            const src = img.attr('src');

            preview.append(`
                <div style="position:relative;display:inline-block;margin:0 8px 8px 0;">
                    <img src="${src}" class="rounded border" style="width:120px;height:120px;object-fit:cover;">
                    <button type="button" style="position:absolute;top:2px;right:6px;background:rgba(0,0,0,0.6);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:18px;line-height:20px;text-align:center;cursor:pointer;" title="Remove Image">&times;</button>
                </div>
            `);

            inputs.append(`<input type="hidden" name="selected_images[${index}][id]" value="${id}">`);
            inputs.append(`<input type="hidden" name="selected_images[${index}][file_path]" value="${src}">`);
            index++;
        });

        preview.find('button').on('click', function () {
            const src = $(this).siblings('img').attr('src');
            const id = $(`.image-box img[src="${src}"]`).parent().data('id').toString();
            selectedMediaIds.delete(id);
            renderSelectedImages();
            $(`.image-box[data-id="${id}"]`).removeClass('selected');
        });
    }

    function loadMoreMedia(categoryId = null) {
        if (isLoading || allMediaLoaded) return;

        isLoading = true;
        $('#mediaLoader').show();

        $.get(`../by-category/${categoryId}?page=${mediaPage}`, function (data) {
            if (data.length === 0) {
                allMediaLoaded = true;
            } else {
                data.forEach(media => {
                    $('#mediaLibrary').append(`
                        <div class="col-md-2 mb-3">
                            <div class="image-box ${selectedMediaIds.has(media.media_id.toString()) ? 'selected' : ''}" data-id="${media.media_id}">
                                <img src="../../public/${media.file_path}" alt="Image" class="img-fluid">
                            </div>
                        </div>
                    `);
                });
                bindImageEvents();
            }
        }).fail(() => {
            $('#mediaLibrary').append('<div class="text-danger">Failed to load more images.</div>');
        }).always(() => {
            $('#mediaLoader').hide();
            isLoading = false;
            mediaPage++;
        });
    }

    function resetMediaLoad(categoryId) {
        $('#mediaLibrary').empty();
        mediaPage = 1;
        allMediaLoaded = false;
        loadMoreMedia(categoryId);
    }

    $(document).ready(function () {
        @if(!empty($product->images))
            @foreach($product->images as $img)
                selectedMediaIds.add("{{ $img->media_id }}");
            @endforeach
        @endif

        bindImageEvents();
        renderSelectedImages();

        $('#confirmSelection').on('click', function () {
            if (!selectedMediaIds.size) {
                alert("Please select at least one image.");
                return;
            }

            renderSelectedImages();
            
            $('#categorySelect').focus();

            const selectedImagesArray = Array.from(selectedMediaIds).map(id => {
                const filePath = $(`.image-box[data-id="${id}"] img`).attr('src');
                return { id: parseInt(id), file_path: filePath };
            });
           document.activeElement.blur();
            bootstrap.Modal.getInstance(document.getElementById('mediaModal')).hide();
        });

        $('#categorySelect').on('change', function () {
            selectedMediaIds.clear();
            renderSelectedImages();
            const categoryId = $(this).val();
            if (categoryId) resetMediaLoad(categoryId);
        });

        $('#mediaModal').on('show.bs.modal', function (e) {
            const categoryId = $('#categorySelect').val();
            if (!categoryId) {
                alert('Please select a category first.');
                e.preventDefault();
                return;
            }
            resetMediaLoad(categoryId);
        });

        $('#mediaScrollContainer').on('scroll', function () {
            const container = $(this);
            if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 10) {
                const categoryId = $('#categorySelect').val();
                loadMoreMedia(categoryId);
            }
        });
    });
</script>