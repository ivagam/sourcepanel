@extends('layout.layout')

@php
    $title = 'SEO';
    $subTitle = 'SEO';
    $script = '';
@endphp

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0">SEO Management</h5>
    </div>

    <div class="card-body p-5">
        <!-- Download Section -->
        <div class="mb-5">
            <label class="form-label fw-semibold fs-6 mb-3 d-block">Download file</label>
            <a href="{{ route('seo.export') }}" class="btn btn-success btn-lg px-5">
                Export Excel
            </a>
        </div>

        <!-- Upload Section -->
        <div>
            <form action="{{ route('seo.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="form-label fw-semibold fs-6 mb-3 d-block">Upload file</label>
                <input type="file" name="file" accept=".xlsx,.xls"
                       class="form-control form-control-lg mb-4" required>
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    Import Excel
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
