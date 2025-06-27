@extends('layout.layout')

@php
    $title = 'Add Category';
    $subTitle = 'Add New Category';
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <h5 class="card-title mb-3">Add Category</h5>
        <form method="POST" action="{{ route('storecategory') }}">
            @csrf

            <div class="mb-3 row align-items-center">
                <label class="col-md-2 col-form-label">Alice Name<span class="text-danger-600">*</span></label>
                <div class="col-md-4">
                    <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror"
                        placeholder="Enter Category Name" value="{{ old('category_name') }}">
                    @error('category_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <label class="col-md-2 col-form-label">Sub Category</label>
                <div class="col-md-4">
                    <select name="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror">
                        <option value="">-- Select Sub Category --</option>
                        @foreach($categorys as $category)
                            <option value="{{ $category->category_id }}" {{ old('subcategory_id') == $category->category_id ? 'selected' : '' }}>
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
                            <option value="{{ $domain->domain_id }}" {{ in_array($domain->domain_id, old('domains', [])) ? 'selected' : '' }}>
                                {{ $domain->domain_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('domains')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button class="btn btn-primary-600" type="submit">Submit</button>
        </form>
    </div>
</div>
@endsection

<script>
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>
