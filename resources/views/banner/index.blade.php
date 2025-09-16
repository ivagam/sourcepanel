@extends('layout.layout')

@php
    $title = 'Banner Management';
    $subTitle = 'Add Banner';
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

            {{-- Add Banner Form --}}
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add Banner</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('storebanner') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3 row align-items-center">

                            <div class="col-md-6">
                                <label class="form-label">Banner Name <span class="text-danger">*</span></label>
                                <input type="text" name="banner_name" class="form-control @error('banner_name') is-invalid @enderror" value="{{ old('banner_name') }}">
                                @error('banner_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                    <label class="form-label">Banner File <span class="text-danger">*</span></label>
                                    <input type="file" name="banner_file" class="form-control @error('banner_file') is-invalid @enderror">
                                    @error('banner_file')
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

            {{-- Banner List --}}
            <div class="col-xxl-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Banner List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Banner Name</th>
                                        <th>File</th>                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bannerFiles as $key => $banner)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $banner->banner_name }}</td>
                                            <td>
                                                @if(in_array(strtolower(pathinfo($banner->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))                                                    
                                                    <img src="{{ asset('public/' . $banner->file_path) }}" alt="Banner Image" style="max-width: 100px; height: auto;">
                                                @else
                                                    <a href="{{ asset('public/' . $banner->file_path) }}" target="_blank">View File</a>
                                                @endif
                                            </td>                                            
                                            <td class="text-center">
                                                <form action="{{ route('deletebanner', $banner->banner_id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this banner?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"><iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon></button>
                                                </form>
                                                
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No banners found.</td>
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
