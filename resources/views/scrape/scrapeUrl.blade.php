@extends('layout.layout')

@php
    $title = 'ScrapeURL List';
    $subTitle = 'All ScrapeURL';
    $script = '<script>
                    let table = new DataTable("#dataTable");
               </script>';
@endphp

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif


<div class="card basic-data-table">
    <div class="card-header">
        <h5 class="card-title mb-0">Scrape URL</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table bordered-table mb-0" id="dataTable" style="min-width: 1000px;" data-page-length='10'>
                <thead>
                    <tr>                        
                        <th class="text-start" style="width: 40%;">URL</th>
                        <th class="text-start" style="width: 15%;">Anchor Text</th>
                        <th class="text-start" style="width: 15%;">Status</th>
                        <th class="text-start" style="width: 15%;">Product Status</th>
                        <th class="text-start" style="width: 15%;">Domain</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scrapeUrl as $url)
                        <tr>
                            <td>
                                <a href="{{ $url->url }}" target="_blank">{{ Str::limit($url->url, 50) }}</a>
                            </td>
                            <td class="text-center">{{ $url->anchor_text ?? '' }}</td>
                            <td class="text-center">{{ ucfirst($url->status) }}</td>
                            <td class="text-center">{{ ucfirst($url->product_status) }}</td>
                            <td class="text-center">{{ $url->domain }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No URLs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

<script>
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow");
        }, 3000);
    });
</script>

