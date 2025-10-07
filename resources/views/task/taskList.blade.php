@extends('layout.layout')

@php
    $title = 'Task List';
    $subTitle = 'All Tasks';
    $script = '<script>
                    let table = new DataTable("#dataTable");
               </script>';
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="card-title mb-0">Task List</h5>
    <div>
        <a href="{{ route('addtask') }}" class="btn btn-primary btn-sm">Add Task</a>
    </div>
</div>

<!-- Task Table -->
<div class="card basic-data-table">
    <div class="card-header">
        <h5 class="card-title mb-0">Task List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" id="dataTable" style="min-width: 1000px;" data-page-length='10'>
                <thead>
                    <tr>
                        <th class="text-start" style="width: 20%;">Action</th>
                        <th class="text-start" style="width: 30%;">Description</th>
                        <th class="text-start" style="width: 15%;">Status</th>
                        <th class="text-start" style="width: 15%;">Priority</th>
                        <th class="text-start" style="width: 20%;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($task as $t)
                        @php
                            $bgColor = '';
                            if($t->status == 'in_progress') {
                                $bgColor = '#FFF9C4';
                            } elseif($t->status == 'completed') {
                                $bgColor = '#bbefc7';
                            }
                        @endphp
                        <tr id="task-row-{{ $t->task_id }}">
                            <td class="text-center" @if($bgColor) style="background-color: {{ $bgColor }} !important;" @endif>
                                <div class="d-flex align-items-center gap-10">
                                    <a href="{{ route('edittask', $t->task_id) }}">
                                        <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                        </button>
                                    </a>
                                    <form action="{{ route('deletetask', $t->task_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </button>
                                    </form>

                                    <!-- 🔄 Status Toggle Button -->
                                    <button type="button" 
                                        class="toggle-status-btn bg-info-focus bg-hover-info-200 text-info-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                        data-id="{{ $t->task_id }}" 
                                        data-status="{{ $t->status }}">
                                        <iconify-icon icon="mdi:refresh" class="menu-icon"></iconify-icon>
                                    </button>
                                </div>
                            </td>
                            <td class="description" @if($bgColor) style="background-color: {{ $bgColor }} !important;" @endif>{{ $t->description }}</td>
                            <td class="status-text" @if($bgColor) style="background-color: {{ $bgColor }} !important;" @endif>{{ ucfirst(str_replace('_', ' ', $t->status)) }}</td>
                            <td @if($bgColor) style="background-color: {{ $bgColor }} !important;" @endif>{{ ucfirst($t->priority) }}</td>
                            <td @if($bgColor) style="background-color: {{ $bgColor }} !important;" @endif>{{ $t->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);

    // 🔄 Toggle Task Status
    $(document).on('click', '.toggle-status-btn', function() {
        let btn = $(this);
        let taskId = btn.data('id');
        let currentStatus = btn.data('status');
        let newStatus = (currentStatus === 'completed') ? 'pending' : 'completed';

        // 🟢 Confirmation message
        if (!confirm('Are you sure you want to mark this task as ' + newStatus + '?')) {
            return;
        }

        $.ajax({
            url: "{{ route('updatetaskstatus') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: taskId
            },
            success: function(res) {
                if (res.success) {
                    btn.data('status', res.new_status);
                    let row = $('#task-row-' + taskId);
                    let newStatus = res.new_status;

                    // 🟩 Update status text
                    row.find('.status-text').text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));

                    // 🟨 Change background color instantly
                    let bgColor = '';
                    if (newStatus === 'completed') bgColor = '#bbefc7';
                    else if (newStatus === 'in_progress') bgColor = '#FFF9C4';
                    else bgColor = '';

                    row.find('td').css('background-color', bgColor);

                    // ✅ Flash message
                    $('<div class="alert alert-success mt-2">Status updated to ' + newStatus + '!</div>')
                        .insertBefore('.card')
                        .delay(2000)
                        .fadeOut('slow');
                }
            },
            error: function() {
                alert('Failed to update status.');
            }
        });
    });
});
</script>
