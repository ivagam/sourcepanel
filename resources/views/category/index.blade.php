@extends('layout.layout')
@php
    $title='Category Management';
    $subTitle = 'Manage Category';
    $script = '';
@endphp


@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row">
            {{-- Add/Edit Form --}}

            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ isset($editcategory) ? 'Edit Category' : 'Add Category' }}</h5>
                    </div>
                    <div class="card-body">
                                                
                    <form method="POST" action="{{ isset($editcategory) ? route('updatecategory', $editcategory->category_id) : route('storecategory') }}">
                        @csrf
                        @if(isset($editcategory))
                            @method('PUT')
                        @endif
                        <div class="mb-3 row align-items-center">
                            <label class="col-md-2 col-form-label">Category Name<span class="text-danger-600">*</span></label>
                            <div class="col-md-4">
                                <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror"
                                    placeholder="Enter Category Name" value="{{ old('category_name', $editcategory->category_name ?? '') }}">
                                @error('category_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <label class="col-md-2 col-form-label">Sub Category</label>
                            <div class="col-md-4">
                                <select name="subcategory_id" id="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror">
                                    <option value="">-- Select Sub Category --</option>
                                    @foreach($categorys as $category)
                                        <option value="{{ $category->category_id }}" 
                                            {{ old('subcategory_id', $editcategory->subcategory_id ?? '') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subcategory_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                          </div>   
                       <div class="mb-3 row align-items-center">
                            <label class="col-md-2 col-form-label">Domains<span class="text-danger-600">*</span></label>
                            <div class="col-md-4">                            
                                <select name="domains[]" class="form-control select2 @error('domains') is-invalid @enderror" multiple>
                                    @foreach ($domains as $domain)
                                        <option value="{{ $domain->domain_id }}"
                                            {{ in_array($domain->domain_id, old('domains', isset($editcategory) ? explode(',', $editcategory->domains) : [])) ? 'selected' : '' }}>
                                            {{ $domain->domain_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('domains')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                                            

                        <button class="btn btn-primary-600" type="submit">{{ isset($editcategory) ? 'Update' : 'Submit' }}</button>
                    </form>
                    </div>
                </div>
            </div>

            {{-- Category List --}}
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                    <div class="card basic-data-table">
                    <div class="card-header">
                    <h5 class="card-title mb-0">Category</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive scroll-sm">

                <div class="d-flex justify-content-end mb-3">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" id="categorySearch" class="form-control" placeholder="Search Category...">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                    </div>
                </div>

                <table class="table bordered-table mb-0" data-page-length='10'>
                <thead>
                            <tr>
                                <th style="text-align: left;">S.L</th>
                                <th>Category Name</th>
                                <th>Sub Category Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorys as $key => $category)
                                <tr>
                                     <td>
                                        <div class="d-flex align-items-center gap-10">                                    
                                            {{ $key + 1 }}
                                        </div>
                                    </td>
                                    <td>{{ $category->category_name }}</td>
                                    <td>
                                        @if($category->subcategory_id == $category->category_id)
                                            {{ $category->category_name }}
                                        @else
                                            <!-- Show subcategory name using the relationship -->
                                            {{ $category->subcategory ? $category->subcategory->category_name : '-' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                    <div class="d-flex align-items-center gap-10 ">
                                        <a href="{{ route('editcategory', $category->category_id) }}">
                                            <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" >
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                        </a>
                                        <form action="{{ route('deletecategory', $category->category_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase?');">
                                            @csrf
                                            @method('DELETE') <!-- Spoof the DELETE method -->
                                            <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                            </button>
                                        </form>                                          
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center">No Categorys found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                  
                </div>
                </div>
                </div>
                
</div>
</div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>
