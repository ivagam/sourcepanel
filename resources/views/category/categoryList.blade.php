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
                            <td>{{ $category->category_name }}</td>
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
@endsection

<script>
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>

