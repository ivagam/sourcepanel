@extends('layout.layout')
@php
    $title = 'Edit Product';
    $subTitle = 'Edit Product';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
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

    .dz-success-icon {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 24px;
    color: green;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    width: 28px;
    height: 28px;
    text-align: center;
    line-height: 28px;
    pointer-events: none;
    z-index: 10;
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
                    <form id="productEditForm" class="row gy-3 needs-validation" method="POST" action="{{ route('updateProduct', $product->product_id) }}" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $product->product_name) }}">
                            @error('product_name')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price <span class="text-danger">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control" value="{{ old('product_price', $product->product_price) }}" >                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Category <span class="text-danger">*</span></label>
                            <select name="category" id="categorySelect" class="form-control select2" >
                                <option value="0">Others</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category', $product->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->full_path ?? $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Domains</label>
                            <select name="domains[]" class="form-control select2" multiple>
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
                            <textarea name="description" class="form-control texteditor">{{ old('description', $product->description) }}</textarea>
                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control">{{ old('meta_keywords', $product->meta_keywords) }}</textarea>
                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>
                            
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Images <span class="text-danger">*</span></label>
                            <div class="dropzone" id="dropzoneEdit"></div>
                        </div>

                        <!-- Store existing image file paths -->
                        @foreach($product->images as $image)
                            <input type="hidden" name="existing_images[]" value="{{ $image->file_path }}">
                        @endforeach

                        <div id="imageOrderBox" class="d-flex flex-wrap mt-3 gap-2">
                            @foreach($product->images->sortBy('serial_no') as $image)
                                <div class="position-relative image-box" data-id="{{ $image->image_id }}">
                                    <img src="{{ env('SOURCE_PANEL_IMAGE_URL') . $image->file_path }}" class="img-thumbnail" style="width: 120px; height: 120px;">
                                    <div class="d-flex justify-content-between mt-1">
                                        <button type="button" class="btn btn-sm btn-secondary move-left" title="Move Left">←</button>
                                        <button type="button" class="btn btn-sm btn-secondary move-right" title="Move Right">→</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dropzone JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>

@php
    $lastSerial = $product->images->max('serial_no') ?? 0;
@endphp

<script>
Dropzone.autoDiscover = false;

let uploadIndex = {{ $lastSerial + 1 }};

const editDropzone = new Dropzone("#dropzoneEdit", {
    url: "{{ route('uploadTempImage') }}",
    method: "POST",
    paramName: "file",
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.webp",
    maxFilesize: 20,
    addRemoveLinks: true,
    dictDefaultMessage: "Drag files or click to upload",
    init: function () {
        this.on("sending", function (file, xhr, formData) {
            formData.append("serial_no", uploadIndex++);
            formData.append("product_id", "{{ $product->product_id }}");
        });
    },
    success: function (file, response) {
    if (response.success) {
        let input = document.createElement('input');
        input.type = "hidden";
        input.name = "existing_images[]";
        input.value = response.file_path;
        document.getElementById("productEditForm").appendChild(input);

        file.existing = true;
        file.filePath = response.file_path;

        let checkmark = document.createElement('div');
        checkmark.className = 'dz-success-icon';
        checkmark.innerHTML = '✔️';
        file.previewElement.appendChild(checkmark);

        // ✅ ADD NEW IMAGE TO IMAGE ORDER BOX FOR REORDERING
        const container = document.createElement('div');
        container.className = "position-relative image-box";
        container.setAttribute("data-id", response.image_id || 'new-' + Date.now()); // Use fallback ID if image_id missing

        container.innerHTML = `
            <img src="{{ env('SOURCE_PANEL_IMAGE_URL') }}/` + response.file_path + `" class="img-thumbnail" style="width: 120px; height: 120px;">
            <div class="d-flex justify-content-between mt-1">
                <button type="button" class="btn btn-sm btn-secondary move-left" title="Move Left">←</button>
                <button type="button" class="btn btn-sm btn-secondary move-right" title="Move Right">→</button>
            </div>
        `;

        document.getElementById("imageOrderBox").appendChild(container);

        container.querySelector('.move-left').addEventListener('click', function () {
            const current = this.closest('.image-box');
            const prev = current.previousElementSibling;
            if (prev) {
                current.parentNode.insertBefore(current, prev);
                updateSerials();
            }
        });

        container.querySelector('.move-right').addEventListener('click', function () {
            const current = this.closest('.image-box');
            const next = current.nextElementSibling;
            if (next) {
                current.parentNode.insertBefore(next, current);
                updateSerials();
            }
        });
    }
},

    error: function (file, errorMessage) {
        alert("Upload failed: " + errorMessage);
    }
});

@foreach($product->images as $index => $image)
{
    let file{{ $index }} = {
        name: "{{ basename($image->file_path) }}",
        size: 123456,
        type: "image/jpeg",
        accepted: true,
        status: Dropzone.SUCCESS,
        existing: true,
        filePath: "{{ $image->file_path }}"
    };
    editDropzone.emit("addedfile", file{{ $index }});
    editDropzone.emit("thumbnail", file{{ $index }}, "{{ env('SOURCE_PANEL_IMAGE_URL') . $image->file_path }}");
    editDropzone.emit("complete", file{{ $index }});
    file{{ $index }}.previewElement.classList.add('dz-success', 'dz-complete');
    editDropzone.files.push(file{{ $index }});
}
@endforeach

editDropzone.on("removedfile", function(file) {
    if (file.existing && file.filePath) {
        // Remove hidden input
        const inputs = document.querySelectorAll('input[name="existing_images[]"]');
        inputs.forEach(input => {
            if (input.value === file.filePath) {
                input.remove();
            }
        });

        // Remove image from reorder box
        document.querySelectorAll('#imageOrderBox .image-box').forEach(box => {
            if (
                box.getAttribute('data-id') === file.image_id?.toString() ||
                box.querySelector('img')?.getAttribute('src')?.includes(file.filePath)
            ) {
                box.remove();
            }
        });

        updateSerials();
        
        // Send delete request to server
        fetch("{{ route('deleteImage') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ file_path: file.filePath })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to delete image: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting image:', error);
        });
    }
});


function updateSerials() {
    let imageOrder = [];
    document.querySelectorAll('#imageOrderBox .image-box').forEach((box, index) => {
        const imageId = box.getAttribute('data-id');
        imageOrder.push({ id: imageId, serial_no: index + 1 });
    });

    fetch("{{ route('updateImageOrder') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ images: imageOrder })
    })
    .then(res => res.json())
    .then(res => {
        if (!res.success) {
            alert("Image order update failed");
        }
    });
}

document.querySelectorAll('.move-left').forEach(btn => {
    btn.addEventListener('click', function () {
        const current = this.closest('.image-box');
        const prev = current.previousElementSibling;
        if (prev) {
            current.parentNode.insertBefore(current, prev);
            updateSerials();
        }
    });
});

document.querySelectorAll('.move-right').forEach(btn => {
    btn.addEventListener('click', function () {
        const current = this.closest('.image-box');
        const next = current.nextElementSibling;
        if (next) {
            current.parentNode.insertBefore(next, current);
            updateSerials();
        }
    });
});
</script>


@endsection
