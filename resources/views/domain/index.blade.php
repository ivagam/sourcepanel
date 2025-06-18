@extends('layout.layout')
@php
    $title = 'Domain Management';
    $subTitle = 'Manage Domain';
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
                        <h5 class="card-title mb-0">{{ isset($editdomain) ? 'Edit Domain' : 'Add Domain' }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ isset($editdomain) ? route('updateDomain', $editdomain->domain_id) : route('storeDomain') }}">
                            @csrf
                            @if(isset($editdomain))
                                @method('PUT')
                            @endif
                            <div class="mb-3 row align-items-center">
                                <label class="col-md-2 col-form-label">Domain Name<span class="text-danger-600">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" name="domain_name" class="form-control @error('domain_name') is-invalid @enderror"
                                        placeholder="Enter Domain Name" value="{{ old('domain_name', $editdomain->domain_name ?? '') }}">
                                    @error('domain_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button class="btn btn-primary-600" type="submit">{{ isset($editdomain) ? 'Update' : 'Submit' }}</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Domain List --}}
            <div class="col-xxl-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Domain List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table bordered-table mb-0" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">S.L</th>
                                        <th>Domain Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($domains as $key => $domain)
                                        <tr>
                                            <td style="text-align: left;">{{ $key + 1 }}</td>
                                            <td>{{ $domain->domain_name }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center gap-10">
                                                    <a href="{{ route('editDomain', $domain->domain_id) }}">
                                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                                        </button>
                                                    </a>
                                                    <form action="{{ route('deleteDomain', $domain->domain_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this domain?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">No domains found.</td></tr>
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
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);
</script>
