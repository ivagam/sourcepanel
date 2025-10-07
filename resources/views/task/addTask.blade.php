@extends('layout.layout')

@php
    $title = 'Add Task';
    $subTitle = 'Add New Task';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card h-100 p-0 radius-12">
    <div class="card-body p-24">
        <h5 class="card-title mb-3">Add Task</h5>

        <form method="POST" action="{{ route('storetask') }}">
            @csrf

            <div class="row">

                <div class="mb-3 col-md-12">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                        placeholder="Enter Task Description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="">-- Select Status --</option>
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                        <option value="">-- Select Priority --</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <button class="btn btn-primary" type="submit">Submit</button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 3000);
    });
</script>
