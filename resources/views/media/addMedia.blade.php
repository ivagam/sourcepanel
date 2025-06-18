@extends('layout.layout')

@php
    $title = 'Media Management';
    $subTitle = 'Add Media';
    $script = '';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row">
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Media</h5>
                    </div>
                    <div class="card-body">
                        <form id="media-dropzone" action="{{ route('storemedia') }}" method="POST" class="dropzone" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3 row align-items-center">
                                <label class="col-md-2 col-form-label">Category<span class="text-danger-600">*</span></label>
                                <div class="col-md-4">
                                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Media File<span class="text-danger-600">*</span></label>
                                <div class="dropzone-area">
                                    <div class="dropzone" id="dropzone"></div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary-600" id="uploadButton">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dropzone CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/dropzone.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>

<script>
    Dropzone.autoDiscover = false;

    const myDropzone = new Dropzone("#dropzone", {
        url: "{{ route('storemedia') }}",
        paramName: "media_file",
        maxFiles: null,
        maxFilesize: 20, // MB
        acceptedFiles: ".jpg,.jpeg,.png,.pdf,.docx,.mp4",
        addRemoveLinks: true,
        autoProcessQueue: false,
        uploadMultiple: false,
        parallelUploads: 10,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        init: function() {
            const dz = this;

            document.getElementById("uploadButton").addEventListener("click", function(e) {
                e.preventDefault();

                let category = document.getElementById("category_id").value;
                if (!category) {
                    alert("Please select a category.");
                    return;
                }

                if (dz.getQueuedFiles().length === 0) {
                    alert("Please add at least one file.");
                    return;
                }

                dz.options.params = { category_id: category };
                dz.processQueue();
            });

            dz.on("queuecomplete", function () {
                window.location.href = "{{ route('mediaList') }}";
            });

            dz.on("error", function (file, response) {
                alert("Upload failed: " + response.message ?? response);
            });
        }
    });

    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>
@endsection
