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
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $product->product_name) }}" required>
                            @error('product_name')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price <span class="text-danger">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control @error('product_price') is-invalid @enderror" value="{{ old('product_price', $product->product_price) }}" required>
                            @error('product_price')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Category <span class="text-danger">*</span></label>
                            <select name="category" id="categorySelect" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')<div class="text-danger">{{ $message }}</div>@enderror
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
                            @error('domains')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Product Description</label>
                            <textarea name="description" class="form-control texteditor @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror">{{ old('meta_keywords', $product->meta_keywords) }}</textarea>
                            @error('meta_keywords')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $product->meta_description) }}</textarea>
                            @error('meta_description')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Media Files <span class="text-danger">*</span></label>
                            <div class="dropzone" id="dropzoneEdit"></div>
                        </div>

                        <!-- Store existing image file paths -->
                        @foreach($product->images as $image)
                            <input type="hidden" name="existing_images[]" value="{{ $image->file_path }}">
                        @endforeach

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

<script>
Dropzone.autoDiscover = false;

const editDropzone = new Dropzone("#dropzoneEdit", {
    url: "#",
    autoProcessQueue: false,
    addRemoveLinks: true,
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.webp",
    maxFilesize: 20,
    parallelUploads: 20,
    dictDefaultMessage: "Drag files or click to upload",
});

// Display existing images
@foreach($product->images as $index => $image)
{
    let file{{ $index }} = {
        name: "{{ basename($image->file_path) }}",
        size: 123456,
        type: "image/jpeg",
        accepted: true,
        status: Dropzone.SUCCESS,
        existing: true
    };
    editDropzone.emit("addedfile", file{{ $index }});
    editDropzone.emit("thumbnail", file{{ $index }}, "{{ url('public/' . $image->file_path) }}");
    editDropzone.emit("complete", file{{ $index }});
    file{{ $index }}.previewElement.classList.add('dz-success', 'dz-complete');
    editDropzone.files.push(file{{ $index }});
}
@endforeach

editDropzone.on("removedfile", function(file) {
    if (file.existing) {
        const inputs = document.querySelectorAll('input[name="existing_images[]"]');
        inputs.forEach(input => {
            if (input.value.includes(file.name)) {
                input.remove();
            }
        });
    }
});

const form = document.getElementById('productEditForm');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    editDropzone.files.forEach(file => {
        if (!file.existing) {
            formData.append('images[]', file, file.name);
        }
    });

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData,
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            console.error(errorData.errors);
            alert('Validation failed.');
            throw new Error('Validation error');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = "{{ route('productList') }}";
        }
    })
    .catch(err => {
        console.error('Submit error:', err);
    });
});
</script>

@endsection
