@extends('layout.layout')

@php
    $title = 'Edit Task';
    $subTitle = 'Update Task Details';
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
        <h5 class="card-title mb-3">Edit Task</h5>

        <form method="POST" action="{{ route('updatetask', $task->task_id) }}">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="mb-3 col-md-12">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                        placeholder="Enter Task Description">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $task->status) == $status ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority }}" {{ old('priority', $task->priority) == $priority ? 'selected' : '' }}>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <button class="btn btn-primary" type="submit">Update Task</button>
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

