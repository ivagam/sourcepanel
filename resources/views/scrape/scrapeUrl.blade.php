@extends('layout.layout')

@php
    $title = 'ScrapeURL List';
    $subTitle = 'All ScrapeURL';
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card basic-data-table">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Scrape URL</h5>
        <button id="deleteSelectedBtn" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" disabled>
            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <form id="bulkDeleteForm" action="{{ route('bulkdeletescrapeurl') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete selected ScrapeUrls?');">
                @csrf
                @method('DELETE')
                <table class="table bordered-table mb-0" id="dataTable" style="min-width: 1000px;" data-page-length='10'>
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th class="text-center" style="width: 10%;">Action</th>
                            <th class="text-start" style="width: 40%;">URL</th>
                            <th class="text-center" style="width: 15%;">Anchor Text</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-center" style="width: 10%;">Product Status</th>
                            <th class="text-center" style="width: 10%;">Domain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scrapeUrl as $url)
                            <tr>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input selectItem" type="checkbox" name="ids[]" value="{{ $url->id }}">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-10">
                                        <form action="{{ route('deletescrapeurl', $url->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ScrapeUrl?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ $url->url }}" target="_blank">{{ $url->url }}</a>
                                </td>
                                <td class="text-center">{{ $url->anchor_text ?? '' }}</td>
                                <td class="text-center">{{ ucfirst($url->status) }}</td>
                                <td class="text-center">{{ ucfirst($url->product_status) }}</td>
                                <td class="text-center">{{ $url->domain }}</td>                                
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

@endsection

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        columnDefs: [
            { orderable: false, targets: [0, 1] },
            { searchable: false, targets: [0, 1] }
        ],
        language: {
            emptyTable: "No URLs found."
        }
    });

    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);

    $('#selectAll').on('change', function() {
        $('.selectItem').prop('checked', this.checked);
        toggleDeleteButton();
    });

    $('.selectItem').on('change', function() {
        $('#selectAll').prop('checked', $('.selectItem:checked').length === $('.selectItem').length);
        toggleDeleteButton();
    });

    function toggleDeleteButton() {
        $('#deleteSelectedBtn').prop('disabled', $('.selectItem:checked').length === 0);
    }

    $('#deleteSelectedBtn').on('click', function() {
        $('#bulkDeleteForm').submit();
    });
});
</script>
