@extends('layout.layout')

@php
    $title = 'Brand Management';
    $subTitle = 'Add Brand';
   $script = '';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 text-center" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 text-center" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <div class="row">

            {{-- Add Brand Form --}}
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Brand</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('storebrand') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3 row align-items-center">

                            <div class="col-md-6">
                                <label class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" name="brand_name" class="form-control @error('brand_name') is-invalid @enderror" value="{{ old('brand_name') }}">
                                @error('brand_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                    <label class="form-label">Brand File <span class="text-danger">*</span></label>
                                    <input type="file" name="brand_file" class="form-control @error('brand_file') is-invalid @enderror">
                                    @error('brand_file')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            

                            <div class="col-md-6">
                                    <label class="form-label">Domains <span class="text-danger">*</span></label>
                                    <select name="domains[]" class="form-control select2 @error('domains') is-invalid @enderror" multiple>
                                        @foreach ($domains as $domain)
                                            <option value="{{ $domain->domain_id }}" {{ collect(old('domains'))->contains($domain->domain_id) ? 'selected' : '' }}>
                                                {{ $domain->domain_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('domains')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">submit</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Brand List --}}
            <div class="col-xxl-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Brand List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Brand Name</th>
                                        <th>File</th>                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($brandFiles as $key => $brand)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $brand->brand_name }}</td>
                                            <td>
                                                @if(in_array(strtolower(pathinfo($brand->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('public/' . $brand->file_path) }}" alt="Brand Image" style="max-width: 100px; height: auto;">
                                                @else
                                                    <a href="{{ asset('public/' . $brand->file_path) }}" target="_blank">View File</a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('deletebrand', $brand->brand_id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this brand?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No brands found.</td>
                                        </tr>
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

@endsection

<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>