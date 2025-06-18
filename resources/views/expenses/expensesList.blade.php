@extends('layout.layout')

@php
    $title = 'Expenses Grid';
    $subTitle = 'Expenses Grid';
    $script = '';
@endphp

@section('content')

<!-- Display the success message -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


            <div class="card basic-data-table">
                <div class="card-header">
                    <h5 class="card-title mb-0">Expenses</h5>
                </div>
                <div class="card-body">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0" data-page-length='10'>
                    <thead>
                    <tr>
                        <th scope="col">
                            <div class="d-flex align-items-center gap-10">
                                <div class="form-check style-check d-flex align-items-center">
                                    <input class="form-check-input radius-4 border input-form-dark" type="checkbox" name="checkbox" id="selectAll">
                                </div>
                                S.L
                            </div>
                        </th>
                        <th scope="col">Amount</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
            @forelse ($expensess as $key => $expenses)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-10">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
                            </div>
                            {{ $key + 1 }}
                        </div>
                    </td>                                                    
                    <td>{{ $expenses->amount }}</td>
                    <td>{{ $expenses->reason }}</td>
                    <td>{{ $expenses->date }}</td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-10 justify-content-center">                            
                            <a href="{{ route('editExpenses', $expenses->expenses_id) }}">
                                <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                </button>
                            </a>
                            <form action="{{ route('deleteExpenses', $expenses->expenses_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this expenses?');">
                                @csrf
                                @method('DELETE') <!-- Spoof the DELETE method -->
                                <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">No expenses found.</td>
                </tr>
            @endforelse
        </tbody>
                    </table>
                </div>
                </div>
            </div>
            
@endsection


<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>
