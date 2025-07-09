@extends('layout.layout')

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<style>

     .image-box {
        transition: border 0.2s ease;
        border: 2px solid transparent;
        border-radius: 5px;
        cursor: pointer;
    }

    .image-box.dragging {
        border: 2px solid green;
        border-radius: 5px;
    }

    .image-box.highlighted {
        border: 2px solid green;
        border-radius: 5px;
        box-shadow: 0 0 8px rgba(0, 128, 0, 0.5);
    }

    .sortable-ghost {
    opacity: 0.4;
    background: #d0ebff;
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
               
                <div class="card-body">
                    <form id="productEditForm" class="row gy-3 needs-validation" method="POST" action="{{ route('updateProduct', $product->product_id) }}" novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="category_id" id="final_category_id" value="{{ old('category_id', $product->category_id) }}">
                        <input type="hidden" name="category_ids" id="category_ids" value="{{ old('category_ids', $product->category_ids) }}">
                        
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Update</button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Uploads Files<span class="text-danger">*</span></label>
                            <div class="dropzone" id="dropzoneEdit"></div>
                        </div>

                        <!-- Store existing image file paths -->
                        @foreach($product->images as $image)
                            <input type="hidden" name="existing_images[]" value="{{ $image->file_path }}">
                        @endforeach

                        <div id="imageOrderBox" class="d-flex flex-wrap mt-3 gap-2">
                            @foreach($product->images->sortBy('serial_no') as $image)
                                <div class="position-relative image-box" data-id="{{ $image->image_id }}" draggable="true" ondragstart="handleDragStart(this)" ondragend="handleDragEnd(this)">
                                    @php
                                        $ext = strtolower(pathinfo($image->file_path, PATHINFO_EXTENSION));
                                        $videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
                                    @endphp

                                    @if(in_array($ext, $videoExtensions))
                                        <video width="120" height="120" controls>
                                            <source src="{{ env('SOURCE_PANEL_IMAGE_URL') . $image->file_path }}" type="video/{{ $ext }}">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <img src="{{ env('SOURCE_PANEL_IMAGE_URL') . $image->file_path }}" class="img-thumbnail" style="width: 120px; height: 120px;">
                                    @endif
                                   
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name', $product->product_name) }}">
                            @error('product_name')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Product Price <span class="text-danger">*</span></label>
                            <input type="number" name="product_price" step="0.01" class="form-control" value="{{ old('product_price', $product->product_price) }}" >                            
                        </div> 

                        <div class="col-md-4">
                            <label class="form-label">Category 1</label>
                            <select class="form-select" id="mainCategorySelect">
                                <option value="">-- Select Main Category --</option>
                                @foreach($mainCategories as $category)
                                    <option value="{{ $category->category_id }}"
                                        {{ old('main_category_id', explode(',', $product->category_ids ?? '')[0] ?? '') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 col-md-8" id="dynamic-subcategories"></div>

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>


@php
    $lastSerial = $product->images->max('serial_no') ?? 0;
@endphp

<script>

    const BASE_URL = "{{ env('SOURCE_PANEL') }}";

function loadSubcategories(parentId, level = 2, selectedId = null) {
    return $.ajax({
        url: `${BASE_URL}category/get-subcategories/${parentId}`,
        type: 'GET',
    }).done(function (response) {
        $(`#dynamic-subcategories .subcat-level`).filter(function () {
            return parseInt($(this).data('level')) >= level;
        }).remove();

        $('#final_category_id').val(parentId);

        if (response.length > 0) {
            let labelNumber = level + 1;
            let options = '<option value="">-- Select Category --</option>';
            response.forEach(cat => {
                options += `<option value="${cat.category_id}" ${selectedId == cat.category_id ? 'selected' : ''}>${cat.category_name}</option>`;
            });

            let dropdown = `
                <div class="col-md-6" data-level="${level}">
                    <label class="form-label">Category ${labelNumber}</label>
                    <select class="form-select" onchange="loadSubcategories(this.value, ${level + 1})">
                        ${options}
                    </select>
                </div>
            `;
            if ($('#dynamic-subcategories .row').length === 0) {
                    $('#dynamic-subcategories').html('<div class="row"></div>');
                }

            $('#dynamic-subcategories .row').append(dropdown);
        }
    }).fail(function () {
        alert('Failed to load subcategories.');
    });
}

async function loadCategoryChain(chain) {
    if (!chain.length) return;

    $('#mainCategorySelect').val(chain[0]);
    for (let i = 1; i < chain.length; i++) {
        await loadSubcategories(chain[i - 1], i, chain[i]);
    }
    $('#final_category_id').val(chain[chain.length - 1]);
}

$(document).ready(function () {
    let categoryIds = $('#category_ids').val();
    let chain = categoryIds ? categoryIds.split(',') : [];

    $('#dynamic-subcategories').html('');
    $('#final_category_id').val('');

    loadCategoryChain(chain);

    $('#mainCategorySelect').on('change', function () {
        const selectedId = $(this).val();
        $('#dynamic-subcategories').html('');
        $('#final_category_id').val('');
        $('#category_ids').val(selectedId || '');

        if (selectedId) {
            loadSubcategories(selectedId, 1);
        }
    });

    $('form').on('submit', function () {
    let selectedIds = [];

    const mainCat = $('#mainCategorySelect').val();
    if (mainCat) {
        selectedIds.push(mainCat);
    }

    $('#dynamic-subcategories select').each(function () {
        const val = $(this).val();
        if (val) {
            selectedIds.push(val);
            $(this).removeClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    $('#category_ids').val(selectedIds.join(','));
    $('#final_category_id').val(selectedIds[selectedIds.length - 1] || '');
});
});


Dropzone.autoDiscover = false;

let uploadIndex = {{ $lastSerial + 1 }};

const editDropzone = new Dropzone("#dropzoneEdit", {
    url: "{{ route('uploadTempImage') }}",
    method: "POST",
    paramName: "file",
    headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi,.webm",
    maxFilesize: 1024,
    timeout: 300000,
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

            const container = document.createElement('div');
            container.className = "position-relative image-box";
            container.setAttribute("data-id", response.image_id || 'new-' + Date.now());

            let isVideo = response.file_path.match(/\.(mp4|mov|avi|webm)$/i);
            let baseUrl = "{{ rtrim(env('SOURCE_PANEL_IMAGE_URL'), '/') }}";
            let previewHTML = isVideo
                ? `<video width="120" height="120" controls>
                    <source src="` + baseUrl + `/` + response.file_path + `" type="` + getMimeType(response.file_path) + `">
                   Your browser does not support the video tag.
                   </video>`
                : `<img src="` + baseUrl + `/` + response.file_path + `" class="img-thumbnail" style="width: 120px; height: 120px;">`;

            container.innerHTML = `${previewHTML}`;

            document.getElementById("imageOrderBox").appendChild(container);
        }
    },
    error: function (file, errorMessage) {
        alert("Upload failed: " + errorMessage);
    }
});

@foreach($product->images as $index => $image)
{
    let ext = "{{ strtolower(pathinfo($image->file_path, PATHINFO_EXTENSION)) }}";
    let mimeType = 'image/jpeg';
    const videoExtensions = ['mp4', 'mov', 'avi', 'webm'];
    if (videoExtensions.includes(ext)) {
        const mimeMap = {
            mp4: 'video/mp4',
            mov: 'video/quicktime',
            avi: 'video/x-msvideo',
            webm: 'video/webm'
        };
        mimeType = mimeMap[ext] || 'video/mp4';
    }
    let file{{ $index }} = {
        name: "{{ basename($image->file_path) }}",
        size: 123456,
        type: mimeType,
        accepted: true,
        status: Dropzone.SUCCESS,
        existing: true,
        filePath: "{{ $image->file_path }}"
    };
    editDropzone.emit("addedfile", file{{ $index }});
    if (mimeType.startsWith('video')) {
    } else {
        editDropzone.emit("thumbnail", file{{ $index }}, "{{ rtrim(env('SOURCE_PANEL_IMAGE_URL'), '/') . '/' . $image->file_path }}");
    }
    editDropzone.emit("complete", file{{ $index }});
    file{{ $index }}.previewElement.classList.add('dz-success', 'dz-complete');
    editDropzone.files.push(file{{ $index }});
}
@endforeach

editDropzone.on("removedfile", function(file) {
    if (file.existing && file.filePath) {
        const inputs = document.querySelectorAll('input[name="existing_images[]"]');
        inputs.forEach(input => {
            if (input.value === file.filePath) {
                input.remove();
            }
        });

        document.querySelectorAll('#imageOrderBox .image-box').forEach(box => {
            if (
                box.getAttribute('data-id') === file.image_id?.toString() ||
                box.querySelector('img')?.getAttribute('src')?.includes(file.filePath) ||
                box.querySelector('video source')?.getAttribute('src')?.includes(file.filePath)
            ) {
                box.remove();
            }
        });

        updateSerials();
        
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


function getMimeType(filePath) {
    const ext = filePath.split('.').pop().toLowerCase();
    switch (ext) {
        case 'mp4': return 'video/mp4';
        case 'mov': return 'video/quicktime';
        case 'webm': return 'video/webm';
        case 'avi': return 'video/x-msvideo';
        default: return 'video/mp4';
    }
}

Sortable.create(document.getElementById('imageOrderBox'), {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function () {
        updateSerials();
    }
});

let lastDragged = null;

    function handleDragStart(element) {
        element.classList.add('dragging');
    }

    function handleDragEnd(element) {
        element.classList.remove('dragging');
        element.classList.add('highlighted');
    }

</script>

@endsection
