@extends('layout.layout')

@php
    $title = 'WhatsApp Management';
    $subTitle = 'Add WhatsApp Message';
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

            {{-- Add WhatsApp Form --}}
            <div class="col-xxl-12">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add WhatsApp Message</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('storewhatsapp') }}">
                            @csrf
                            <div class="mb-3 row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="3">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Shortcut <span class="text-danger">*</span></label>
                                    <input type="text" name="shortcut" class="form-control @error('shortcut') is-invalid @enderror" value="{{ old('shortcut') }}">
                                    @error('shortcut')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- WhatsApp List --}}
            <div class="col-xxl-12 mt-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">WhatsApp Messages List</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive scroll-sm">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Message</th>
                                        <th>Shortcut</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($whatsappMessages as $key => $whatsapp)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $whatsapp->message }}</td>
                                            <td>{{ $whatsapp->shortcut }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('deletewhatsapp', $whatsapp->id) }}" method="POST" onsubmit="return confirm('Are you sure to delete this message?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No WhatsApp messages found.</td>
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
