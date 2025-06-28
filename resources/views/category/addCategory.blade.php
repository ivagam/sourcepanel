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

            <div class="mb-3">
                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="category_name" class="form-control @error('category_name') is-invalid @enderror"
                    placeholder="Enter Category Name" value="{{ old('category_name') }}">
                @error('category_name')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Sub Category</label>
                <select name="subcategory_id" class="form-select">
                    <option value="">-- Select Sub Category --</option>
                    @foreach($categorys as $category)
                        <option value="{{ $category->category_id }}" {{ old('subcategory_id') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>                
            </div>

            <div class="mb-3">
                <label class="form-label">Alice Name </label>
                <input type="text" name="alice_name" class="form-control "
                    placeholder="Enter Alice Name" value="{{ old('alice_name') }}">               
            </div>

            <div class="mb-3">
                <label class="form-label">Domains </label>
                <select name="domains[]" class="form-control select2 " multiple>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->domain_id }}" {{ in_array($domain->domain_id, old('domains', [])) ? 'selected' : '' }}>
                            {{ $domain->domain_name }}
                        </option>
                    @endforeach
                </select>              
            </div>

            <button class="btn btn-primary" type="submit">Submit</button>
        </form>
    </div>
</div>

@endsection

<script>
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>
