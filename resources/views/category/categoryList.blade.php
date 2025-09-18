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

<!-- Add the two buttons at the top-right corner without removing any existing styles -->
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
                <div class="mb-3">
                    <label for="delete_name" class="form-label">Category Name to Delete</label>
                    <input type="text" class="form-control" id="delete_name" name="delete_name" required>
                </div>
                <p class="text-muted">Only categories matching exactly this name will be deleted.</p>
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

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 3000);
    });
</script>

