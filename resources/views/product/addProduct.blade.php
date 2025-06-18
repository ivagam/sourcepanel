@extends('layout.layout')
@php
    $title = 'Add Product';
    $subTitle = 'Add Product';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<style>

    .custom-style {
        box-sizing: border-box;
        font-size: 100%;
        margin-top: 5px;
        margin-left: 5px;
        max-width: 100%;
        font-family: sans-serif;
        word-break: keep-all;
    }
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
                    <h5 class="card-title mb-0">Add Product</h5>
                </div>
                <div class="card-body">
                    <form class="row gy-3 needs-validation" method="POST" action="{{ route('storeproduct') }}" novalidate>
                        @csrf
                        
                        <div class="col-md-6">
                            <label class="form-label">Product Name<span class="text-danger-600">*</span></label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name') }}" required>
                            @error('product_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price<span class="text-danger-600">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control @error('product_price') is-invalid @enderror" placeholder="Enter Product Price" value="{{ old('product_price') }}" required>
                            @error('product_price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Category<span class="text-danger-600">*</span></label>
                            <select name="category" id="categorySelect" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Domains<span class="text-danger-600">*</span></label>
                            <select name="domains[]" class="form-control select2 @error('domains') is-invalid @enderror" multiple>
                                @foreach ($domains as $domain)
                                    <option value="{{ $domain->domain_id }}" {{ collect(old('domains'))->contains($domain->domain_id) ? 'selected' : '' }}>
                                        {{ $domain->domain_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('domains')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Product Description</label>
                            <textarea name="description" class="form-control texteditor @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror">{{ old('meta_keywords') }}</textarea>
                            @error('meta_keywords')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description') }}</textarea>
                            @error('meta_description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mt-3">
                            <button type="button" class="btn btn-info" id="openMediaModal">
                                Add Images
                            </button>
                        </div>

                        <!-- Image Preview -->
                        <div class="col-md-12 mt-3" id="selectedImagePreview" style="display: none;">                            
                            <div class="d-flex flex-wrap gap-2 mb-2" id="previewContainer"></div>
                        </div>

                        <!-- Hidden Inputs for Selected Image IDs -->
                        <div id="selectedImageInputs"></div>

                        <div class="col-md-12">
                            <button class="btn btn-primary-600" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Modal -->
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
                            <div class="image-box {{ in_array($image->media_id, old('selected_images.*.media_id', [])) ? 'selected' : '' }}" data-id="{{ $image->media_id }}">
                                <img src="../public/{{ $image->file_path }}" alt="Image" class="img-fluid">
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
    let page = 1;
    let loading = false;
    const perPage = 30;
    let categoryId = null;

    function bindImageEvents() {
        $('.image-box').off('click').on('click', function () {
            const id = $(this).data('id').toString();
            $(this).toggleClass('selected');
            selectedMediaIds.has(id) ? selectedMediaIds.delete(id) : selectedMediaIds.add(id);
        });
    }

    function renderSelectedImages() {
        const previewContainer = $('#previewContainer').empty();
        const inputsContainer = $('#selectedImageInputs').empty();
        let index = 0;

        selectedMediaIds.forEach(id => {
            const img = $(`.image-box[data-id="${id}"] img`);
            const imgSrc = img.attr('src');

            const container = $(`
                <div style="position:relative; display:inline-block; margin:0 8px 8px 0;">
                    <img src="${imgSrc}" class="rounded border" style="width:120px; height:120px; object-fit:cover;">
                    <span style="position:absolute; top:2px; right:6px; color:white; background-color:rgba(0,0,0,0.6); border-radius:50%; width:20px; height:20px; line-height:20px; text-align:center; cursor:pointer; font-weight:bold;">×</span>
                </div>
            `);


            container.find('span').on('click', () => {
                container.remove();
                selectedMediaIds.delete(id);
                $(`.image-box[data-id="${id}"]`).removeClass('selected');
                if (!selectedMediaIds.size) $('#selectedImagePreview').hide();
                inputsContainer.find(`input[value="${id}"], input[value="${imgSrc}"]`).remove();
            });

            previewContainer.append(container);

            inputsContainer.append(`
                <input type="hidden" name="selected_images[${index}][id]" value="${id}">
                <input type="hidden" name="selected_images[${index}][file_path]" value="${imgSrc}">
            `);
            index++;
        });

        $('#selectedImagePreview').show();
    }

    function loadImages(pageNumber) {
        if (!categoryId || loading) return;

        loading = true;
        $('#mediaLoader').show();

        $.ajax({
            url: `by-category/${categoryId}?page=${pageNumber}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.length === 0) return;

                response.forEach(media => {
                    const selectedClass = selectedMediaIds.has(media.media_id.toString()) ? 'selected' : '';
                    $('#mediaLibrary').append(`
                        <div class="col-md-2 mb-3">
                            <div class="image-box ${selectedClass}" data-id="${media.media_id}">
                                <img src="../public/${media.file_path}" alt="Image" class="img-fluid">
                            </div>
                        </div>
                    `);
                });

                bindImageEvents();
                loading = false;
                $('#mediaLoader').hide();
            },
            error: function (xhr) {
                console.error('Error loading media:', xhr.responseText);
                loading = false;
                $('#mediaLoader').hide();
            }
        });
    }

    $(document).ready(function () {
        // Open modal only if category selected
        $('#openMediaModal').on('click', function () {
            const selected = $('#categorySelect').val();
            if (!selected) {
                alert("Please select a category first.");
                return;
            }
            categoryId = selected;
            page = 1;
            loading = false;
            $('#mediaLibrary').empty();
            loadImages(page);
            new bootstrap.Modal(document.getElementById('mediaModal')).show();
        });

        // Scroll for pagination inside modal
        $('#mediaScrollContainer').on('scroll', function () {
            const scrollTop = $(this).scrollTop();
            const scrollHeight = $(this)[0].scrollHeight;
            const clientHeight = $(this).innerHeight();

            if (!loading && scrollTop + clientHeight >= scrollHeight - 10) {
                page++;
                loadImages(page);
            }
        });

        // Re-highlight already selected images when modal shown
        $('#mediaModal').on('shown.bs.modal', function () {
            $('#mediaLibrary .image-box').each(function () {
                const id = $(this).data('id').toString();
                $(this).toggleClass('selected', selectedMediaIds.has(id));
            });
        });

        // Confirm image selection
        $('#confirmSelection').on('click', function () {
            if (!selectedMediaIds.size) {
                alert("Please select at least one image.");
                return;
            }

            renderSelectedImages();

            const selectedImagesArray = Array.from(selectedMediaIds).map(id => {
                const filePath = $(`.image-box[data-id="${id}"] img`).attr('src');
                return { id: parseInt(id), file_path: filePath };
            });

            $('#selected_images_input').val(JSON.stringify(selectedImagesArray));
            bootstrap.Modal.getInstance(document.getElementById('mediaModal')).hide();
            $(this).blur();
        });
    });
</script>

