@extends('layout.layout')

@php
    $title = 'Category List';
    $subTitle = 'All Categories';
    $script = '<script>
                    let table = new DataTable("#dataTable");
               </script>';
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title mb-0">Category List</h5>
    <div>
        <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
    </div>
</div>

<div class="card basic-data-table">
    <div class="card-header">
        <h5 class="card-title mb-0">Category List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" id="dataTable" style="min-width: 1000px;" data-page-length='10'>
                <thead>
                    <tr>
                        <th class="text-start" style="width: 10%;">Action</th>
                        <th class="text-start" style="width: 30%;">Category Name</th>
                        <th class="text-start" style="width: 30%;">Alice Name</th>
                        <th class="text-start" style="width: 30%;">Sub Category Name</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorys as $key => $category)
                        <tr>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10">
                                    <a href="{{ route('editcategory', $category->category_id) }}">
                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>
                                    <form action="{{ route('deletecategory', $category->category_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('search') }}?category_id={{ $category->category_id }}">
                                    {{ $category->category_name }} ({{ $category->products_count }})
                                </a>
                            </td>
                            <td>{{ $category->alice_name }}</td>
                            <td>{{ $category->subcategory ? $category->subcategory->category_name : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('bulkEditCategory') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="old_name" class="form-label">Current Category Name</label>
                    <input type="text" class="form-control" id="old_name" name="old_name" required>
                </div>
                <div class="mb-3">
                    <label for="new_name" class="form-label">New Category Name</label>
                    <input type="text" class="form-control" id="new_name" name="new_name" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('bulkDeleteCategory') }}">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Level 1 -->
                <div class="mb-3">
                    <label class="form-label">Category 1</label>
                    <select class="form-select" id="level1">
                        <option value="">-- Select --</option>
                        @foreach($categorys->where('subcategory_id', 113) as $cat)
                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level 2 -->
                <div class="mb-3">
                    <label class="form-label">Category 2</label>
                    <select class="form-select" id="level2">
                        <option value="">-- Select --</option>
                    </select>
                </div>

                <!-- Level 3 -->
                <div class="mb-3">
                    <label class="form-label">Category 3</label>
                    <select class="form-select" id="level3">
                        <option value="">-- Select --</option>
                    </select>
                </div>
                
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const BASE_URL = "{{ env('SOURCE_PANEL') }}";

$(document).ready(function() {
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);

    // Level 1 → Level 2
    $('#level1').change(function() {
        let id = $(this).val();
        $('#level2').html('<option value="">-- Select --</option>');
        $('#level3').html('<option value="">-- Select --</option>');
        if (!id) return;

        $.get(BASE_URL + "category/get-subcategories/" + id, function(data) {
            data.forEach(cat => {
                $('#level2').append(`<option value="${cat.category_id}">${cat.category_name}</option>`);
            });
        });
    });

    // Level 2 → Level 3
    $('#level2').change(function() {
        let id = $(this).val();
        $('#level3').html('<option value="">-- Select --</option>');
        if (!id) return;

        $.get(BASE_URL + "category/get-subcategories/" + id, function(data) {
            data.forEach(cat => {
                $('#level3').append(`<option value="${cat.category_id}">${cat.category_name}</option>`);
            });
        });
    });

    // Form submit: allow any selected level
    $('#deleteModal form').on('submit', function(e) {
        let selectedId = $('#level3').val() || $('#level2').val() || $('#level1').val();
        if (!selectedId) {
            alert('Please select at least one category.');
            e.preventDefault();
            return false;
        }
        // add hidden input dynamically
        if ($('#category_id').length === 0) {
            $(this).append('<input type="hidden" name="category_id" id="category_id" />');
        }
        $('#category_id').val(selectedId);
    });
});
</script>