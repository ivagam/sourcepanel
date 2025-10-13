@extends('layout.layout')

@php
    $title = 'Add Category';
    $subTitle = 'Add New Category';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <h5 class="card-title mb-3">Category 2 Keywords</h5>

        <!-- 🔹 NEW FORM: Show all categories directly -->
        <form method="POST" action="{{ route('updateAllAliceNames') }}">
            @csrf

            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            {{ implode(' ', array_slice(explode(' ', $category->category_name), 1)) }}
                        </label>
                        <input type="text"
                               name="alice_names[{{ $category->category_id }}]"
                               class="form-control"
                               placeholder="Category Search Keyword"
                               value="{{ $category->alice_name ?? '' }}">
                    </div>
                @endforeach
            </div>

            <button class="btn btn-success mt-3" type="submit">
                Save
            </button>
        </form>
    </div>
</div>

<hr class="my-5">

<!-- 🔹 EXISTING FORM: Dropdown version stays below -->
<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <h5 class="card-title mb-3">Category 3 Keywords</h5>

        <form method="POST" action="{{ route('updateAliceNames') }}">
            @csrf

            <!-- Category Dropdown -->
            <div class="mb-3 col-md-6">
                <label class="form-label">Category</label>
                <select class="form-select" name="subcategory_id" id="categorySelect">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}">
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Subcategories + Alice Name inputs will appear here -->
            <div class="row" id="subcategory-container"></div>

            <button class="btn btn-primary mt-3" type="submit" id="updateButton" style="display:none;">
                Update
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Use SOURCE_PANEL from .env
    const BASE_URL = "{{ env('SOURCE_PANEL') }}";

    $('#categorySelect').on('change', function() {
        const categoryId = $(this).val();
        $('#subcategory-container').html('');
         $('#updateButton').hide();

        if (!categoryId) return;

        $.ajax({
            url: BASE_URL + "category/get-subcategories/" + categoryId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.length > 0) {
                    response.forEach(function(subcat) {
                        const row = `
                            <div class="col-md-6 mb-3">
                                <label class="form-label">${subcat.category_name}</label>
                                <input type="text"
                                       name="alice_names[${subcat.category_id}]"
                                       class="form-control"
                                       placeholder="Category Search Keyword"
                                       value="${subcat.alice_name || ''}">
                            </div>
                        `;
                        $('#subcategory-container').append(row);
                    });
                    $('#updateButton').show();
                } else {
                    $('#subcategory-container').append(
                        '<div class="col-12"><em>No subcategories found.</em></div>'
                    );
                }
            },
            error: function(xhr) {
                console.error("AJAX Error:", xhr.responseText);
                alert('Failed to load subcategories.');
            }
        });
    });

});
</script>