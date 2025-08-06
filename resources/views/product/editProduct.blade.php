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
                    <form id="productEditForm" class="row gy-3 needs-validation" method="POST"
                        action="{{ route('updateProduct', ['id' => $product->product_id] + ($isDuplicate ? ['duplicate' => 1] : [])) }}"
                        novalidate enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <input type="hidden" name="category_id" id="final_category_id" value="{{ old('category_id', $product->category_id) }}">
                        <input type="hidden" name="category_ids" id="category_ids" value="{{ old('category_ids', $product->category_ids) }}">
                        
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-4 align-items-center">
                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input category-toggle" type="checkbox" value="1" id="checkboxWatches">
                                    <label class="form-check-label mb-0" for="checkboxWatches">Watches</label>
                                </div>

                                <div class="form-check d-flex align-items-center gap-2">
                                    <input class="form-check-input category-toggle" type="checkbox" value="113" id="checkboxOther1">
                                    <label class="form-check-label mb-0" for="checkboxOther1">Other</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 justify-content-end">
                            <button type="submit" name="is_updated" value="0" class="btn btn-primary">Update</button>
                            <button type="submit" name="is_updated" value="1" class="btn btn-success">Complete</button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Uploads Files</label>
                            <div class="dropzone" id="dropzoneEdit"></div>
                        </div>

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
                            <label class="form-label">Product Price</label>
                            <input type="number" name="product_price" step="0.01" class="form-control" value="{{ old('product_price', $product->product_price) }}" >                            
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


                        <div class="row" id="colorSizeInputs" style="display: none; margin: 0; padding: 0;">
                            <div class="col-md-6">
                                <label class="form-label">Color</label>
                                <div class="input-group">
                                    <!-- Color Picker -->
                                    <input 
                                        type="color" 
                                        id="colorPicker" 
                                        class="form-control form-control-color" 
                                        value="{{ old('color', $product->color ?? '#000000') }}"
                                    >

                                    <!-- Hex Manual Input -->
                                    <input 
                                        type="text" 
                                        id="colorInput" 
                                        name="color" 
                                        class="form-control" 
                                        placeholder="#000000" 
                                        value="{{ old('color', $product->color ?? '') }}"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Size</label>
                                <input 
                                    type="text" 
                                    name="size" 
                                    class="form-control" 
                                    placeholder="Enter size" 
                                    value="{{ old('size', $product->size ?? '') }}"
                                >
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Product Description</label>
                            <textarea name="description" class="form-control texteditor">{{ old('description', $product->description) }}</textarea>                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Purchase value</label>
                            <input type="number" name="purchase_value" id="purchase_value" class="form-control" value="{{ old('purchase_value', $product->purchase_value) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Purchase code</label>
                            <input type="text" name="purchase_code" id="purchase_code" class="form-control" value="{{ old('purchase_code', $product->purchase_code) }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control texteditor">{{ old('note', $product->note) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" >
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

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <textarea name="meta_keywords" class="form-control">{{ old('meta_keywords', $product->meta_keywords) }}</textarea>
                            
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control">{{ old('meta_description', $product->meta_description) }}</textarea>                            
                        </div>

                        <div class="col-md-12 d-flex gap-3">
                            <button type="submit" name="is_updated" value="0" class="btn btn-primary">Update</button>
                            <button type="submit" name="is_updated" value="1" class="btn btn-success">Complete</button>
                        </div>

                    </form>
                </div>
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

function resetSubcategories() {
    $('#watch-subcategories').html('').hide();           // clear + hide watch subcategories
    $('#dynamic-subcategories').html('<div class="row"></div>').hide();  // clear + hide dynamic subcategories
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
                `<option value="${cat.category_id}" ${selectedId == cat.category_id ? 'selected' : ''}>${cat.category_name}</option>`
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
            let mediaUrl = baseUrl + '/' + response.file_path;
             let previewHTML;
                if (isVideo) {
                    previewHTML = `<video width="120" height="120" style="cursor: pointer;" onclick="showFullMedia('${mediaUrl}', 'video', '${response.file_path.split('.').pop()}')">
                                        <source src="${mediaUrl}" type="${getMimeType(response.file_path)}">
                                        Your browser does not support the video tag.
                                </video>`;
                } else {
                    previewHTML = `<img src="${mediaUrl}" class="img-thumbnail" style="width: 120px; height: 120px; cursor: pointer;" onclick="showFullMedia('${mediaUrl}', 'image')">`;
                }

                container.innerHTML = previewHTML;
                document.getElementById("imageOrderBox").appendChild(container);
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

document.addEventListener('DOMContentLoaded', function () {
    const mainCategorySelect = document.getElementById('mainCategorySelect');
    const colorSizeInputs = document.getElementById('colorSizeInputs');
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('colorInput');

    function toggleColorSizeInputs() {
        const selectedValue = parseInt(mainCategorySelect.value);
        if (!isNaN(selectedValue) && selectedValue !== 1) {
            colorSizeInputs.style.display = 'flex';
        } else {
            colorSizeInputs.style.display = 'none';
        }
    }

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

let currentAjax = null;

$(document).ready(function () {
    const categoryIds = $('#category_ids').val();
    const mainCatId = categoryIds ? categoryIds.split(',')[0] : null;

    if (mainCatId === '1') {
        $('#checkboxWatches').prop('checked', true);
    } else if (mainCatId === '113') {
        $('#checkboxOther1').prop('checked', true);
    }

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
        }
    });
});

document.getElementById('purchase_value').addEventListener('input', function () {
        const value = this.value.trim();

        if (!value || isNaN(value)) {
            document.getElementById('purchase_code').value = '';
            return;
        }

        const numberToLetter = {
            '1': 'A', '2': 'B', '3': 'C', '4': 'D',
            '5': 'E', '6': 'F', '7': 'G', '8': 'H', '9': 'I'
        };

        const getRandomLetter = () => {
            const chars = 'abcdefghijklmnopqrstuvwxyz';
            return chars.charAt(Math.floor(Math.random() * chars.length));
        };

        const getRandomLetters = (length) => {
            let result = '';
            for (let i = 0; i < length; i++) {
                result += getRandomLetter();
            }
            return result;
        };

        let converted = '';
        for (let digit of value) {
            if (digit === '0') {
                converted += getRandomLetter(); // add one random lowercase for each 0
            } else {
                converted += numberToLetter[digit] || '';
            }
        }

        // Build final code: 3 lowercase letters + converted value
        const finalCode = getRandomLetters(3) + converted;
        document.getElementById('purchase_code').value = finalCode;
    });

</script>

@endsection