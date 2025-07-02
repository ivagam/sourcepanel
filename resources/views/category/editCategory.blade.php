@extends('layout.layout')

@php
    $title = 'Edit Category';
    $subTitle = 'Edit Existing Category';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <h5 class="card-title mb-3">Edit Category</h5>

        <form method="POST" action="{{ route('updatecategory', $editcategory->category_id) }}">
            @csrf
            @method('PUT')

            <!-- Category Name -->
              <div class="row">
            <div class="mb-3 col-md-6">
                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror"
                    placeholder="Enter Category Name" value="{{ old('category_name', $editcategory->category_name) }}">
                @error('category_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Alice Name -->
            <div class="mb-3 col-md-6">
                <label class="form-label">Alice Name</label>
                <input type="text" name="alice_name" class="form-control"
                    placeholder="Enter Alice Name" value="{{ old('alice_name', $editcategory->alice_name) }}">
            </div>
        </div>

            <!-- Main Category select (top level) -->
            <div class="mb-3">
                <label class="form-label">Main Category</label>
                <select class="form-select" id="mainCategorySelect">
                    <option value="">-- Select Main Category --</option>
                    @foreach($mainCategories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ old('main_category_id', $mainCategoryId) == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Container for dynamic subcategory selects -->
            <div class="mb-3" id="dynamic-subcategories"></div>

           <input type="hidden" name="subcategory_id" id="final_subcategory_id" value="{{ old('subcategory_id', $editcategory->subcategory_id) }}">
           <input type="hidden" name="category_ids" id="category_ids" value="{{ old('category_ids', $editcategory->category_ids) }}">

            <!-- Domains -->
            <div class="mb-3">
                <label class="form-label">Domains</label>
                <select name="domains[]" class="form-control select2" multiple>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->domain_id }}"
                            {{ in_array($domain->domain_id, old('domains', explode(',', $editcategory->domains))) ? 'selected' : '' }}>
                            {{ $domain->domain_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <button class="btn btn-primary" type="submit">Update</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const BASE_URL = "{{ env('SOURCE_PANEL') }}";

    setTimeout(() => $(".alert").fadeOut("slow"), 3000);

    function loadSubcategories(parentId, level = 1, selectedId = null) {
    return $.ajax({
        url: `${BASE_URL}category/get-subcategories/${parentId}`,
        type: 'GET',
    }).done(function(response) {        
        $(`#dynamic-subcategories .subcat-level`).filter(function () {
            return parseInt($(this).data('level')) >= level;
        }).remove();

        $('#final_subcategory_id').val(parentId);

        if (response.length > 0) {
            let options = '<option value="">-- Select Sub Category --</option>';
            response.forEach(cat => {
                options += `<option value="${cat.category_id}" ${selectedId == cat.category_id ? 'selected' : ''}>${cat.category_name}</option>`;
            });

            let dropdown = `
                <div class="mb-3 subcat-level" data-level="${level}">
                    <label class="form-label">Sub Category (Level ${level})</label>
                    <select class="form-select" onchange="loadSubcategories(this.value, ${level + 1})">
                        ${options}
                    </select>
                </div>
            `;
            $('#dynamic-subcategories').append(dropdown);
        }
    }).fail(function() {
        alert('Failed to load subcategories.');
    });
}

async function loadCategoryChain(chain) {
    if (!chain.length) return;
    
    $('#mainCategorySelect').val(chain[0]);
    
    for (let i = 1; i < chain.length; i++) {
        await loadSubcategories(chain[i - 1], i, chain[i]);
    }
}

$(document).ready(function () {
    let categoryIds = $('#category_ids').val();
    let chain = categoryIds ? categoryIds.split(',') : [];

    $('#dynamic-subcategories').html('');
    $('#final_subcategory_id').val('');

    loadCategoryChain(chain);

    $('#mainCategorySelect').on('change', function () {
        const selectedId = $(this).val();
        $('#dynamic-subcategories').html('');
        $('#final_subcategory_id').val('');
        $('#category_ids').val(selectedId || '');

        if (selectedId) {
            loadSubcategories(selectedId, 1);
        }
    });

    $('form').on('submit', function(e) {
        let selectedIds = [];

        const mainCat = $('#mainCategorySelect').val();
        if (!mainCat) {
            alert('Please select the main category.');
            e.preventDefault();
            return false;
        }
        selectedIds.push(mainCat);

        let allSelected = true;
        $('#dynamic-subcategories select').each(function() {
            const val = $(this).val();
            if (!val) {
                allSelected = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
                selectedIds.push(val);
            }
        });

        if (!allSelected) {
            alert('Please select all subcategories.');
            e.preventDefault();
            return false;
        }

        $('#category_ids').val(selectedIds.join(','));
        $('#final_subcategory_id').val(selectedIds[selectedIds.length - 1]);
    });

});
</script>