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
        <form id="bulkDeleteForm" action="{{ route('bulkdeletescrapeurl') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete selected Scrape URLs?');">
            @csrf
            @method('DELETE')
            <button id="deleteSelectedBtn" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" disabled>
                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
            </button>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" style="min-width: 1000px;">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">
                            <input class="form-check-input" type="checkbox" id="selectAll">
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
                                <input class="form-check-input selectItem" type="checkbox" name="ids[]" value="{{ $url->id }}">
                            </td>
                            <td class="text-center">
                                <form action="{{ route('deletescrapeurl', $url->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Scrape URL?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                    </button>
                                </form>
                            </td>
                            <td><a href="{{ $url->url }}" target="_blank">{{ $url->url }}</a></td>
                            <td class="text-center">{{ $url->anchor_text ?? '' }}</td>
                            <td class="text-center">{{ ucfirst($url->status) }}</td>
                            <td class="text-center">{{ ucfirst($url->product_status) }}</td>
                            <td class="text-center">{{ $url->domain }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Scrape URLs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ✅ Laravel Pagination --}}
        <div class="mt-3 text-end">
            {{ $scrapeUrl->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Fade out alerts
    setTimeout(() => $(".alert").fadeOut("slow"), 3000);

    // Checkbox logic
    $('#selectAll').on('change', function() {
        $('.selectItem').prop('checked', this.checked);
        toggleDeleteButton();
    });

    $(document).on('change', '.selectItem', function() {
        $('#selectAll').prop('checked', $('.selectItem:checked').length === $('.selectItem').length);
        toggleDeleteButton();
    });

    function toggleDeleteButton() {
        $('#deleteSelectedBtn').prop('disabled', $('.selectItem:checked').length === 0);
    }

    // Submit bulk delete
    $('#deleteSelectedBtn').on('click', function(e) {
        e.preventDefault();
        $('#bulkDeleteForm').submit();
    });
});
</script>
@endpush
