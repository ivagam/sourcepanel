@extends('layout.layout')
@php
    $title = 'Add Product';
    $subTitle = 'Add Product';
@endphp

@section('content')

@error('images')
    <div class="text-danger">{{ $message }}</div>
@enderror

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
                    <h5 class="card-title mb-0">Add Product</h5>
                </div>
                <div class="card-body">                    
                    <form id="productForm" class="row gy-3 needs-validation" method="POST" action="{{ route('storeproduct') }}" novalidate enctype="multipart/form-data">
                        @csrf
                        
                        <div class="col-md-6">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Enter Product Name" value="{{ old('product_name') }}" required>
                            @error('product_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price <span class="text-danger">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control @error('product_price') is-invalid @enderror" placeholder="Enter Product Price" value="{{ old('product_price') }}" required>
                            @error('product_price')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="categorySelect" class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach ($categories as $category)
                                     <option value="{{ $category->category_id }}" 
                                        {{ old('category_id', 1509) == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Domains <span class="text-danger">*</span></label>
                            <select name="domains[]" class="form-control select2 @error('domains') is-invalid @enderror" multiple required>
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

                        <div class="mb-3">
                            <label class="form-label">Media Files <span class="text-danger">*</span></label>
                            <div class="dropzone" id="dropzone"></div>
                        </div>

                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dropzone CSS/JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>

<script>
  Dropzone.autoDiscover = false;

  const myDropzone = new Dropzone("#dropzone", {
    url: "/fake-url", // dummy, no actual upload on file add
    autoProcessQueue: false, // disable auto upload
    addRemoveLinks: true,
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.webp,.pdf,.docx,.mp4",
    maxFilesize: 20,
    parallelUploads: 20,
    dictDefaultMessage: "Drag files here or click to upload",
  });

  const form = document.getElementById('productForm');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(form);
    myDropzone.files.forEach(file => {
        formData.append('images[]', file, file.name);
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
            // Handle validation errors here
            console.error(errorData.errors);
            alert('Validation failed. Check console.');
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
