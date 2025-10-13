@extends('layout.layout')
@php
    $title='Terms & Conditions';
    $subTitle = 'Terms & Conditions';
    $script = '<script src="' . asset('assets/js/editor.highlighted.min.js') . '"></script>
                <script src="' . asset('assets/js/editor.quill.js') . '"></script>
                <script src="' . asset('assets/js/editor.katex.min.js') . '"></script>
                <script>
                // Editor Js Start
                const quill = new Quill("#editor", {
                    modules: {
                        syntax: true,
                        toolbar: "#toolbar-container",
                    },
                    placeholder: "Compose an epic...",
                    theme: "snow",
                });
                // Editor Js End
                </script>';
@endphp
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

#editor, #editor_en {
    height: 150px;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #ddd;
    padding: 10px;
    background: #fff;
}
.ql-editor{
    min-height:120px !important;
    border:1px solid #000 !important;
}

.form-buttons {
    position: sticky;
    top: 0;
    background: #fff;
    padding: 10px 0;
    z-index: 999;
    display: flex;
    gap: 10px;
}

</style>

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="col-lg-12">
            <div class="card">               
                <form id="productEditForm" class="row gy-3 needs-validation" method="POST"
                        action="{{ route('updateProduct', ['id' => $product->product_id] + ($isDuplicate ? ['duplicate' => 1] : [])) }}"
                        novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="category_id" id="final_category_id" value="{{ old('category_id', $product->category_id) }}">
                        <input type="hidden" name="category_ids" id="category_ids" value="{{ old('category_ids', $product->category_ids) }}">
                        
                        <div class="form-buttons sticky-top-buttons">
                            <button type="submit" name="is_updated" value="0" class="btn btn-primary">Update</button>
                            <button type="submit" name="is_updated" value="1" class="btn btn-success">Complete</button>
                            <button type="submit" name="is_product_c" value="1" class="btn btn-warning">Is Product C</button>
                        </div>

                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-4 align-items-center">

                                <!-- Group 1: Category Checkboxes (same column: e.g., category_id) -->
                                <div class="d-flex gap-4">
                                    <!-- Watches -->
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input category-toggle" type="checkbox" name="category[]" value="1" id="checkboxWatches">
                                        <label class="form-check-label mb-0" for="checkboxWatches">Watches</label>
                                    </div>

                                    <!-- Other -->
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input category-toggle" type="checkbox" name="category[]" value="113" id="checkboxOther1">
                                        <label class="form-check-label mb-0" for="checkboxOther1">Other</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">                           
                             <p id="reverseImagesBtn1" style="text-align:right;padding:0px;margin:0px;cursor:pointer"></p>
                            <label class="form-label">Uploads Files</label>
                            <div class="dropzone" id="dropzoneEdit"></div>
                        </div>
                        <p id="reverseImagesBtn2" style="text-align:right;padding:0px;margin:0px;cursor:pointer"></p>
                        <!-- Store existing image file paths -->
                        @foreach($selectedImages as $image)
                            <input type="hidden" name="existing_images[]" value="{{ $image->file_path }}">
                        @endforeach                        
                        <div id="imageOrderBox" class="d-flex flex-wrap mt-3 gap-2">
                           
                            @foreach($selectedImages->sortBy('serial_no')->values() as $index => $image)
                                @php
                                    $ext = strtolower(pathinfo($image->file_path, PATHINFO_EXTENSION));
                                    $mediaUrl = env('SOURCE_PANEL_IMAGE_URL') . $image->file_path;
                                    $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                                @endphp

                                <div class="position-relative image-box" data-id="{{ $image->image_id }}">
                                    @if($isVideo)
                                        <video width="120" height="120" controls style="cursor: pointer;" onclick="event.stopPropagation(); showFullMedia({{ $index }})">
                                            <source src="{{ $mediaUrl }}" type="video/{{ $ext }}">
                                        </video>
                                    @else
                                        <img src="{{ $mediaUrl }}" class="img-thumbnail" style="width: 120px; height: 120px; cursor: pointer;" onclick="showFullMedia({{ $index }})">
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
                            <label class="form-label">Product Value</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" >
                        </div>

                        <div class="row" style="margin: 0; padding: 0;">
                            <div class="col-md-3" id="colorSizeBox" style="display: none;">
                                <label class="form-label">Color & Size</label>
                                <input 
                                    type="text" 
                                    name="size" 
                                    class="form-control" 
                                    placeholder="Enter size" 
                                    value="{{ old('size', $product->size ?? '') }}"
                                >
                                <div class="d-flex gap-1">
                                    <input 
                                        type="color" 
                                        id="colorPicker" 
                                        class="form-control form-control-color form-control-sm" 
                                        value="{{ old('color', $product->color ?? '#000000') }}"
                                        style="width: 30%; min-width: 40px;"
                                    >

                                    <input 
                                        type="text" 
                                        id="colorInput" 
                                        name="color" 
                                        class="form-control form-control-sm" 
                                        placeholder="#000000" 
                                        value="{{ old('color', $product->color ?? '') }}"
                                        style="width: 70%;"
                                    >
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Numbers</label>
                                <input 
                                    type="number" 
                                    name="purchase_value" 
                                    id="purchase_value" 
                                    class="form-control" 
                                    value="{{ old('purchase_value', $product->purchase_value) }}"
                                >
                            </div>

                            <!-- Purchase Code -->
                            <div class="col-md-3">
                                <label class="form-label">Purchase Code</label>
                                <input 
                                    type="text" 
                                    name="purchase_code" 
                                    id="purchase_code" 
                                    class="form-control" 
                                    value="{{ old('purchase_code', $product->purchase_code) }}"
                                >
                            </div>

                            <!-- Product Price -->
                            <div class="col-md-3">
                                <label class="form-label">Product Price</label>
                                <input 
                                    type="number" 
                                    name="product_price" 
                                    step="0.01" 
                                    class="form-control" 
                                    value="{{ old('product_price', $product->product_price) }}"
                                >
                            </div>
                        </div>

                    
                        <div class="col-md-6" style="display: none;">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="mainCategorySelect" disabled>
                                <option value="">-- Select Main Category --</option>
                                @foreach($mainCategories as $category)
                                    <option value="{{ $category->category_id }}"
                                        {{ old('main_category_id', explode(',', $product->category_ids ?? '')[0] ?? '') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="watch-subcategories" class="col-md-12" style="display: none;"></div>

                        <div class="col-md-12" id="dynamic-subcategories"></div>

                        <!-- Input 2: English Description -->
                        <div class="col-md-6">
                            <label class="form-label">Product Description (English)</label>
                            <div class="card-body p-0">
                                <div id="toolbar-container-en">
                                    <span class="ql-formats">
                                        <select class="ql-font"></select>
                                        <select class="ql-size"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                    </span>
                                </div>
                                <div id="editor_en">{!! old('description_en', $product->description_en ?? $product->description) !!}</div>
                                <textarea name="description_en" id="description_en" hidden></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Enter Chinese Text</label>
                            <div class="card-body p-0">
                                <div id="toolbar-container-input"class="mb-12">
                                    <span class="ql-formats">
                                        <select class="ql-font"></select>
                                        <select class="ql-size"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                    </span>
                                </div>
                                <div id="editor_input"></div>
                                <textarea name="input_chinese" id="input_chinese" hidden></textarea>
                            </div>
                        </div>                        

                        <!-- Input 3: Chinese Description -->
                        <div class="col-md-6">
                            <label class="form-label">Chinese Description</label>
                            <div class="card-body p-0">
                                <div id="toolbar-container">
                                    <span class="ql-formats">
                                        <select class="ql-font"></select>
                                        <select class="ql-size"></select>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered"></button>
                                        <button class="ql-list" value="bullet"></button>
                                        <button class="ql-indent" value="-1"></button>
                                        <button class="ql-indent" value="+1"></button>
                                    </span>
                                </div>
                                <div id="editor">{!! old('chinese_description', $product->chinese_description ?? '') !!}</div>
                                <textarea name="chinese_description" id="chinese_description" hidden></textarea>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control texteditor">{{ old('note', $product->note) }}</textarea>
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

                        <div class="col-md-12 d-flex gap-3">
                            <button type="submit" name="is_updated" value="0" class="btn btn-primary">Update</button>
                            <button type="submit" name="is_updated" value="1" class="btn btn-success">Complete</button>
                            <button type="submit" name="is_product_c" value="1" class="btn btn-warning">Is Product C</button>
                        </div>

                    </form>
            </div>
        </div>
    </div>
</div>

@php
    $mediaItems = $product->images->sortBy('serial_no')->values()->map(function($img) {
        $ext = strtolower(pathinfo($img->file_path, PATHINFO_EXTENSION));
        return [
            'type' => in_array($ext, ['mp4', 'mov', 'avi', 'webm']) ? 'video' : 'image',
            'url' => env('SOURCE_PANEL_IMAGE_URL') . $img->file_path,
            'ext' => $ext
        ];
    });
@endphp

<!-- Modal -->
<div class="modal fade" id="mediaPreviewModal" tabindex="-1" aria-labelledby="mediaPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header border-0">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <div id="mediaCarousel" class="carousel slide" data-bs-interval="false">
          <div class="carousel-inner" id="mediaCarouselInner"></div>

          <!-- Controls -->
          <button class="carousel-control-prev" type="button" data-bs-target="#mediaCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#mediaCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
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
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@php
    $lastSerial = $product->images->max('serial_no') ?? 0;
@endphp

<script>

    const BASE_URL = "{{ env('SOURCE_PANEL') }}";

$(document).ready(function () {
    const categoryIds = $('#category_ids').val();
    const chain = categoryIds ? categoryIds.split(',') : [];
    const mainCategoryFromUrl = '{{ $mainCategory ?? '' }}';
    const mainCategory = chain.length ? chain[0] : mainCategoryFromUrl;

    if (mainCategory == '1') {
        loadWatchSubcategories(mainCategory, chain);
    } else if (mainCategory == '113') {
        if (chain.length) {
            loadCategoryChain(chain);
        } else {
            loadSubcategories('113', 1);
        }
    }

    $('#mainCategorySelect').on('change', function () {
        const selectedId = $(this).val();
        
        resetSubcategories(); 
        
        $('#category_ids').val(selectedId || '');

        if (selectedId === '1') {
            loadWatchSubcategories(selectedId, []);
        } else if (selectedId) {
            loadSubcategories(selectedId, 1);
        }
    });


    $('form').on('submit', function () {
        const selectedIds = [];
        const mainCat = $('#mainCategorySelect').val();

        if (mainCat) {
            selectedIds.push(mainCat);
            const wrapper = mainCat === '1' ? '#watch-subcategories' : '#dynamic-subcategories';
            $(`${wrapper} select`).each(function () {
                const val = $(this).val();
                if (val) selectedIds.push(val);
            });
        }

        $('#category_ids').val(selectedIds.join(','));
        $('#final_category_id').val(selectedIds.at(-1) || '');
    });
});

    function resetSubcategories(show = false) {
        $('#watch-subcategories').html('').hide();
        $('#dynamic-subcategories').html('<div class="row"></div>');

        if (show) $('#dynamic-subcategories').show(); // Show only when needed

        $('#final_category_id').val('');
    }


function loadWatchSubcategories(parentId, chain = []) {
    return $.ajax({
        url: `${BASE_URL}category/get-watch-subcategories/${parentId}`,
        type: 'GET',
    }).done(function (response) {
        if (!response.length) return;

        let html = '<div class="row">';
        response.forEach((group, index) => {
            const selectedVal = chain[index + 1] || '';
            html += `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${group.category_name}</label>
                    <select class="form-select">
                        <option value="">-- Select ${group.category_name} --</option>
                        ${group.children.map(child =>
                            `<option value="${child.category_id}" ${selectedVal == child.category_id ? 'selected' : ''}>${child.category_name}</option>`
                        ).join('')}
                    </select>
                </div>`;
        });
        html += '</div>';
        $('#watch-subcategories').html(html).show();
    }).fail(() => {
        console.warn('Watch subcategories request aborted or failed.');
    });
}

async function loadCategoryChain(chain) {
    if (!chain.length) return;

    $('#mainCategorySelect').val(chain[0]);
    for (let i = 1; i < chain.length; i++) {
        await loadSubcategories(chain[i - 1], i, chain[i]);
    }
    $('#final_category_id').val(chain.at(-1));
}


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
    maxFilesize: 1024, // MB
    timeout: 300000,
    addRemoveLinks: true,
    dictDefaultMessage: "Drag files or click to upload",

    init: function () {
        this.on("sending", function (file, xhr, formData) {
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
            file.imageId = response.image_id;

            let checkmark = document.createElement('div');
            checkmark.className = 'dz-success-icon';
            checkmark.innerHTML = '✔️';
            file.previewElement.appendChild(checkmark);

            // preview container
            const container = document.createElement('div');
            container.className = "position-relative image-box";
            container.setAttribute("data-id", response.image_id || 'new-' + Date.now());

            let isVideo = response.file_path.match(/\.(mp4|mov|avi|webm)$/i);
            let baseUrl = "{{ rtrim(env('SOURCE_PANEL_IMAGE_URL'), '/') }}";
            let mediaUrl = baseUrl + '/' + response.file_path;

            let previewHTML;
            if (isVideo) {
                previewHTML = `
                    <video width="120" height="120" style="cursor: pointer;"
                        onclick="showFullMedia('${mediaUrl}', 'video', '${response.file_path.split('.').pop()}')">
                        <source src="${mediaUrl}" type="${getMimeType(response.file_path)}">
                        Your browser does not support the video tag.
                    </video>`;
            } else {
                previewHTML = `
                    <img src="${mediaUrl}" class="img-thumbnail"
                        style="width: 120px; height: 120px; cursor: pointer;"
                        onclick="showFullMedia('${mediaUrl}', 'image')">`;
            }

            container.innerHTML = previewHTML;
            document.getElementById("imageOrderBox").appendChild(container);
            updateReverseButtonText();
        }
    },

    error: function (file, errorMessage) {
        alert("Upload failed: " + errorMessage);
    }
});

@foreach($selectedImages as $index => $image)
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
        filePath: "{{ $image->file_path }}",
        image_id: "{{ $image->image_id }}"
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
            body: JSON.stringify({ image_id: file.image_id })
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
        updateReverseButtonText();
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

const items = @json($mediaItems);

function showFullMedia(startIndex = 0) {
    const inner = document.getElementById('mediaCarouselInner');
    inner.innerHTML = '';

    items.forEach((item, index) => {
        const isActive = index === startIndex ? 'active' : '';
        const content = item.type === 'video'
            ? `<video controls autoplay muted playsinline style="max-height:75vh; max-width:100%; border-radius:8px;">
                  <source src="${item.url}" type="video/${item.ext}">
                  Your browser does not support the video tag.
              </video>`
            : `<img src="${item.url}" class="d-block w-100" style="max-height: 75vh; object-fit: contain;">`;

        inner.innerHTML += `<div class="carousel-item ${isActive} text-center">${content}</div>`;
    });

    const carouselElement = document.getElementById('mediaCarousel');
    let carousel = bootstrap.Carousel.getInstance(carouselElement);
    if (!carousel) {
        carousel = new bootstrap.Carousel(carouselElement, { interval: false, wrap: true });
    }
    carousel.to(startIndex);

    new bootstrap.Modal(document.getElementById('mediaPreviewModal')).show();
}

let currentAjax = null;

$(document).ready(function () {
    const categoryIds = $('#category_ids').val();
    const mainCatId = categoryIds ? categoryIds.split(',')[0] : null;

    // Pre-check main category
    if (mainCatId === '1') {
        $('#checkboxWatches').prop('checked', true);
    } else if (mainCatId === '113') {
    $('#checkboxOther1').prop('checked', true);

    const productInput = $('#product_name');
    if (productInput.length) {
        const productName = (productInput.val() || '').trim();
        const firstWord = productName.split(' ')[0].toLowerCase();

        const category1Select = $('#dynamic-subcategories select').first();
        let matched = false;

        if (category1Select.length) {
            category1Select.find('option').each(function() {
                const optionText = $(this).text().trim().toLowerCase();
                if(optionText === firstWord) {
                    $(this).prop('selected', true);
                    matched = true;
                    return false; // break loop
                }
            });

            if(matched) {
                const selectedCategory1 = category1Select.val();
                if(selectedCategory1) {
                    loadSubcategories(selectedCategory1, 2);
                }
            }
        }
    }
}


    calculatePurchase();

    // Handle category toggle
    $('.category-toggle').on('change', function () {
        if (this.checked) {
            $('.category-toggle').not(this).prop('checked', false);

            const selectedCategory = $(this).val();

            if (currentAjax) currentAjax.abort();

            resetSubcategories();

            $('#mainCategorySelect').val(selectedCategory).trigger('change');
            $('#category_ids').val(selectedCategory);
            $('#final_category_id').val(selectedCategory);

            if (selectedCategory === '1') {
                currentAjax = loadWatchSubcategories(selectedCategory, []);
            } else {
                currentAjax = loadSubcategories(selectedCategory, 1);
            }
            toggleColorSizeInputs();

            calculatePurchase(); // Keep calculation after toggle
        }
    });

    // Update purchase on input
    $('#purchase_value').on('input', calculatePurchase);
});

    function toggleColorSizeInputs() {
        const selectedValue = document.getElementById('mainCategorySelect').value;
        const colorSizeBox = document.getElementById('colorSizeBox');

        if (selectedValue === '113') {
            colorSizeBox.style.display = 'block';
        } else {
            colorSizeBox.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('mainCategorySelect').addEventListener('change', toggleColorSizeInputs);
        toggleColorSizeInputs();
    });


    document.addEventListener('DOMContentLoaded', function () {
        const mainCategorySelect = document.getElementById('mainCategorySelect');
        const colorPicker = document.getElementById('colorPicker');
        const colorInput = document.getElementById('colorInput');

        colorPicker.addEventListener('input', function () {
            colorInput.value = colorPicker.value;
        });

        colorInput.addEventListener('input', function () {
            const val = colorInput.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                colorPicker.value = val;
            }
        });

        toggleColorSizeInputs();
        mainCategorySelect.addEventListener('change', toggleColorSizeInputs);
    });


    function calculatePurchase() {
        const purchaseValueInput = $('#purchase_value');
        const purchaseCodeInput = $('#purchase_code');
        const productPriceInput = $('input[name="product_price"]');
        const categoryCheckboxes = $('.category-toggle');

        function getSelectedCategory() {
            return categoryCheckboxes.filter(':checked').val() || null;
        }

        const value = parseFloat(purchaseValueInput.val()) || 715;
        const mainCategory = getSelectedCategory();
        if (!mainCategory) return;

        const numberToLetter = {
            '1':'A','2':'B','3':'C','4':'D','5':'E','6':'F','7':'G','8':'H','9':'I'
        };

        const getRandomLetter = () => 'abcdefghijklmnopqrstuvwxyz'.charAt(Math.floor(Math.random() * 26));
        const getRandomLetters = (length) => Array.from({length}, getRandomLetter).join('');

        let converted = '';
        for (let digit of value.toString()) {
            converted += digit === '0' ? getRandomLetter() : numberToLetter[digit] || '';
        }

        purchaseCodeInput.val(getRandomLetters(4) + converted);

        let productPrice = 0;
        const dividedValue = value / 7;

        if (mainCategory === '113') {
            if (dividedValue <= 65) productPrice = dividedValue + 40;
            else if (dividedValue <= 199) productPrice = dividedValue * 1.6;
            else productPrice = dividedValue * 1.5;
        } else if (mainCategory === '1') {
            if (dividedValue <= 100) productPrice = dividedValue + 80;
            else if (dividedValue <= 199) productPrice = dividedValue + 90;
            else if (dividedValue <= 339) productPrice = dividedValue + 100;
            else productPrice = dividedValue * 1.3;
        }

        const allowedDigits = [2,4,6,8];
        let n = Math.round(productPrice);
        const lastDigit = n % 10;
        const closest = allowedDigits.reduce((prev,curr)=>{
            const prevDiff = Math.abs(lastDigit-prev), currDiff = Math.abs(lastDigit-curr);
            if(currDiff<prevDiff) return curr;
            if(currDiff===prevDiff) return Math.max(curr,prev);
            return prev;
        });
        productPriceInput.val(n - lastDigit + closest);
    }

    function loadSubcategories(parentId, level = 2, selectedId = null) {
        return $.ajax({
            url: `${BASE_URL}category/get-subcategories/${parentId}`,
            type: 'GET',
        }).done(function (response) {
        $(`#dynamic-subcategories .subcat-level`).filter(function () {
            return parseInt($(this).data('level')) >= level;
        }).remove();

        $('#final_category_id').val(parentId);

        if (!response.length) return;

        const labelNumber = level;
        const options = [
            `<option value="">-- Select Category --</option>`,
            ...response.map(cat =>
                `<option value="${cat.category_id}" 
                    ${selectedId == cat.category_id ? 'selected' : ''} 
                    data-alice_name="${cat.alice_name || ''}">
                    ${cat.category_name}
                </option>`
            )
        ].join('');

        const dropdown = `
            <div class="col-md-6 subcat-level" data-level="${level}">
                <label class="form-label">Category ${labelNumber}</label>
                <select class="form-select" onchange="loadSubcategories(this.value, ${level + 1})">
                    ${options}
                </select>
            </div>
        `;

        if (!$('#dynamic-subcategories .row').length) {
            $('#dynamic-subcategories').html('<div class="row"></div>');
        }

        $('#dynamic-subcategories .row').append(dropdown);
        $('#dynamic-subcategories').show();
    }).fail(() => {
        console.warn('Subcategories request aborted or failed.');
    });
}

    document.addEventListener("DOMContentLoaded", function () {    
    var quill = new Quill("#editor", {
        modules: { toolbar: "#toolbar-container" },
        theme: "snow",
        formats: ['font','size','bold','list','indent']
    });

    const hiddenInput = document.querySelector("#description");

    quill.clipboard.dangerouslyPasteHTML(hiddenInput.value);

    quill.on("text-change", function () {
        hiddenInput.value = quill.root.innerHTML;
    });

    document.querySelector("form").addEventListener("submit", function () {
        hiddenInput.value = quill.root.innerHTML;
    });
    });


const container = document.getElementById('imageOrderBox');
const reverseBtn1 = document.getElementById('reverseImagesBtn1');
const reverseBtn2 = document.getElementById('reverseImagesBtn2');

function updateReverseButtonText() {
    const count = container.querySelectorAll('.image-box img, .image-box video').length;
    reverseBtn1.textContent = `Reverse Images (${count})`;
    reverseBtn2.textContent = `Reverse Images (${count})`;
}

function reverseImages() {
    const boxes = Array.from(container.querySelectorAll('.image-box'));
    const imageBoxes = boxes.filter(box => box.querySelector('img'));
    const videoBoxes = boxes.filter(box => box.querySelector('video'));

    imageBoxes.reverse();

    container.innerHTML = '';
    imageBoxes.forEach(box => container.appendChild(box));
    videoBoxes.forEach(box => container.appendChild(box));

    updateSerials();
    updateReverseButtonText();
}

updateReverseButtonText();

[reverseBtn1, reverseBtn2].forEach(btn => {
    btn.addEventListener('click', reverseImages);
});


document.addEventListener('DOMContentLoaded', function () {
    const productInput = document.querySelector('input[name="product_name"]');
    const mainCategorySelect = document.getElementById('mainCategorySelect');
    if (!productInput || !mainCategorySelect) return;

    let popupOpen = false; // ✅ prevents re-running while popup open

    function clearAllSubcategories(container) {
        const selects = container.querySelectorAll('.subcat-level select');
        selects.forEach(select => {
            select.value = "";
            if (window.jQuery) $(select).trigger('change');
            else select.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    function setCategorySelect(select, matchOption) {
        if (matchOption) {
            select.value = matchOption.value;
            if (window.jQuery) $(select).trigger('change');
            else select.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            select.value = "";
            select.selectedIndex = 0;
            if (window.jQuery) $(select).trigger('change');
            else select.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

   function showCategoryPopup(matches, callback) {
        popupOpen = true;

        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.background = 'rgba(0,0,0,0.3)';
        overlay.style.zIndex = 9998;

        const popup = document.createElement('div');
        popup.style.position = 'fixed';
        popup.style.top = '50%';
        popup.style.left = '50%';
        popup.style.transform = 'translate(-50%, -50%)';
        popup.style.background = '#fff';
        popup.style.padding = '20px';
        popup.style.borderRadius = '10px';
        popup.style.zIndex = 9999;
        popup.style.minWidth = '280px';
        popup.style.boxShadow = '0 4px 10px rgba(0,0,0,0.3)';
        popup.style.fontFamily = 'Arial, sans-serif';

        const title = document.createElement('div');
        title.textContent = 'Multiple Category matches found:';
        title.style.fontWeight = 'bold';
        title.style.marginBottom = '10px';
        title.style.fontSize = '15px';
        popup.appendChild(title);

        // ✅ checkboxes with instant confirm logic
        matches.forEach((opt, index) => {
            const label = document.createElement('label');
            label.style.display = 'flex';
            label.style.alignItems = 'center';
            label.style.marginBottom = '8px';
            label.style.cursor = 'pointer';
            label.style.gap = '8px';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = 'categoryPopup';
            checkbox.value = index;
            checkbox.style.appearance = 'auto';
            checkbox.style.width = '16px';
            checkbox.style.height = '16px';
            checkbox.style.cursor = 'pointer';

            // ✅ instant confirm when clicked
            checkbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    // Uncheck all others
                    popup.querySelectorAll('input[name="categoryPopup"]').forEach(cb => {
                        if (cb !== e.target) cb.checked = false;
                    });

                    // Immediately confirm
                    const selectedIndex = parseInt(e.target.value, 10);
                    callback(matches[selectedIndex]);

                    // Close popup
                    document.body.removeChild(popup);
                    document.body.removeChild(overlay);
                    popupOpen = false;
                }
            });

            const text = document.createElement('span');
            text.textContent = opt.text;
            text.style.fontSize = '14px';

            label.appendChild(checkbox);
            label.appendChild(text);
            popup.appendChild(label);
        });

        document.body.appendChild(overlay);
        document.body.appendChild(popup);
    }

    function runCategoryMatch() {
        if (popupOpen) return;
        if (mainCategorySelect.value !== '113') return;

        const words = (productInput.value || '').trim().split(/\s+/);
        const lowerWords = words.map(w => w.toLowerCase());
        const categoryContainer = document.querySelector('#dynamic-subcategories');
        if (!categoryContainer) return;

        const category1Select = categoryContainer.querySelector('.subcat-level[data-level="1"] select');
        if (!category1Select) return;

        // ---------- CATEGORY 1 MATCH ----------
        let match1 = null;
        for (const word of lowerWords) {
            match1 = Array.from(category1Select.options)
                .find(opt => opt.text.trim().toLowerCase().includes(word));
            if (match1) break;
        }

        setCategorySelect(category1Select, match1);

        // ---------- CATEGORY 2 MATCH ----------
        const category2Observer = new MutationObserver((mutations, obs) => {
            const category2Select = categoryContainer.querySelector('.subcat-level[data-level="2"] select');
            if (category2Select) {
                let match2 = null;
                const options = Array.from(category2Select.options);

                outerLoop:
                for (const opt of options) {
                    const rawAlice = opt.dataset.alice_name || opt.text || "";
                    const aliceParts = rawAlice.split(',').map(p => p.trim().toLowerCase()).filter(Boolean);

                    for (const alice of aliceParts) {
                        for (const word of lowerWords) {
                            if (word === alice) {
                                match2 = opt;
                                break outerLoop;
                            }
                        }
                    }
                }

                setCategorySelect(category2Select, match2);
                obs.disconnect();

                // ---------- CATEGORY 3 MATCH ----------
                const category3Observer = new MutationObserver((mutations, obs3) => {
                const category3Select = categoryContainer.querySelector('.subcat-level[data-level="3"] select');
                if (category3Select) {
                    const options3 = Array.from(category3Select.options);
                    const matchedOptions = [];
                    const seenValues = new Set();

                    // helper to normalize tokens (lowercase + strip non-alnum)
                    const normalize = s => (s || '').toLowerCase().replace(/[^a-z0-9]/g, '').trim();

                    outer:
                    for (const opt of options3) {
                        const rawAlice = opt.dataset.alice_name || "";
                        if (!rawAlice.trim()) continue;

                        // split on comma, slash, pipe, etc — more robust than only comma
                        const aliceParts = rawAlice.split(/[,\/|]+/)
                            .map(p => p.trim().toLowerCase())
                            .filter(Boolean);

                        for (const alice of aliceParts) {
                            const aliceNorm = normalize(alice);
                            for (const word of lowerWords) {
                                const wordNorm = normalize(word);

                                // match exact or singular/plural variants (simple heuristic)
                                if (
                                    wordNorm === aliceNorm ||
                                    wordNorm === aliceNorm.replace(/s$/, '') ||
                                    wordNorm.replace(/s$/, '') === aliceNorm
                                ) {
                                    // dedupe by option value (so same option isn't pushed twice)
                                    if (!seenValues.has(opt.value)) {
                                        matchedOptions.push(opt);
                                        seenValues.add(opt.value);
                                    }
                                    // once this option matched, skip remaining aliceParts/words
                                    continue outer;
                                }
                            }
                        }
                    }

                    if (matchedOptions.length === 1) {
                        setCategorySelect(category3Select, matchedOptions[0]);
                    } else if (matchedOptions.length > 1) {
                        // safety: don't open multiple popups
                        if (!popupOpen) {
                            showCategoryPopup(matchedOptions, selected => {
                                setCategorySelect(category3Select, selected);
                            });
                        }
                    }

                    obs3.disconnect();
                }
            });
            category3Observer.observe(categoryContainer, { childList: true, subtree: true });
            }
        });
        category2Observer.observe(categoryContainer, { childList: true, subtree: true });
    }

    productInput.addEventListener('blur', runCategoryMatch);
    productInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            runCategoryMatch();
        }
    });
});


function setCategory2Value(select, matchCategory) {
    const matchOption = Array.from(select.options)
        .find(opt => opt.text.trim().toLowerCase().includes(matchCategory.toLowerCase()));
    if (matchOption) {
        select.value = matchOption.value;
        setTimeout(() => {
            if (window.jQuery) $(select).trigger('change');
            else select.dispatchEvent(new Event('change', { bubbles: true }));
        }, 100);
    }
}



document.addEventListener("DOMContentLoaded", function () {
    const quillInput = new Quill("#editor_input", {
        modules: { toolbar: "#toolbar-container-input" },
        theme: "snow"
    });
    const quillEn = new Quill("#editor_en", {
        modules: { toolbar: "#toolbar-container-en" },
        theme: "snow"
    });
    const quillCn = new Quill("#editor", {
        modules: { toolbar: "#toolbar-container" },
        theme: "snow"
    });

    const hiddenInput = document.getElementById("input_chinese");
    const hiddenEn = document.getElementById("description_en");
    const hiddenCn = document.getElementById("chinese_description");

    if (!hiddenInput || !hiddenEn || !hiddenCn) {
        console.error("One or more hidden fields are missing!");
        return;
    }

    function containsChinese(text) {
        return /[\u4e00-\u9fff]/.test(text);
    }

    async function translateFree(text) {
        const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=zh&tl=en&dt=t&q=${encodeURIComponent(text)}`;
        try {
            const res = await fetch(url);
            const data = await res.json();
            return data[0].map(item => item[0]).join('') || text;
        } catch (e) {
            return text;
        }
    }

    quillInput.root.addEventListener("blur", async function () {
        let text = quillInput.getText().trim();
        if (!text) return;

        hiddenInput.value = quillInput.root.innerHTML;

        if (containsChinese(text)) {
            quillCn.root.innerHTML = quillInput.root.innerHTML;
            hiddenCn.value = quillInput.root.innerHTML;

            const translated = await translateFree(text);
            quillEn.root.innerHTML = translated;
            hiddenEn.value = translated;

            quillInput.setText('');
            hiddenInput.value = '';
        }
    });

    quillInput.on("text-change", () => hiddenInput.value = quillInput.root.innerHTML);
    quillCn.on("text-change", () => hiddenCn.value = quillCn.root.innerHTML);
    quillEn.on("text-change", () => hiddenEn.value = quillEn.root.innerHTML);

    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", () => {
            hiddenInput.value = quillInput.root.innerHTML;
            hiddenCn.value = quillCn.root.innerHTML;
            hiddenEn.value = quillEn.root.innerHTML;
        });
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const productNameInput = document.querySelector("input[name='product_name']");
    const sizeInput = document.querySelector("input[name='size']");
    
    function setSizeByProductName(name) {
        const title = name.toLowerCase();

        if (/shirt|hoodie|jacket|cardigan|sweater|coat|jeans|pants|shorts|under garment|bikini|scarf|swim|vest|dress|jumper/.test(title)) {
            return "S,M,L,XL,XXL";
        } else if (/handbag|shopping|tote|clutch|wallet|purse/.test(title)) {
            return "75 cms";
        } else if (/sneakers|boot|loafers|ballerina|sandal|slide|mule|moccasin|slippers|flip flop|chappal/.test(title)) {
            return "EU35-46";
        } else if (/belt/.test(title)) {
            return "90-125 cms";
        }
        return "";
    }

    function updateSize() {
        const newSize = setSizeByProductName(productNameInput.value);

        if (newSize) {
            sizeInput.value = newSize;
        }        
    }

    productNameInput.addEventListener("input", updateSize);

    updateSize();
});
</script>

@endsection