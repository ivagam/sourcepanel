@extends('layout.layout')

@php
    $title = 'Parse Image';
    $subTitle = 'Parse Image';
@endphp

@section('content')
<div class="container">
    <h6>{{ $title }}</h6>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- URL Input Form --}}
    <form action="{{ route('parseImage.url') }}" method="POST" class="mb-4">
        @csrf
        <div class="d-flex">
            <input type="text" name="page_url" 
                class="form-control me-3" 
                placeholder="Enter a webpage URL" 
                required 
                style="width:80%;"
                value="{{ session('page_url') }}">
            
            <button type="submit" class="btn btn-primary" style="width:15%;">Fetch</button>
        </div>
    </form>

    {{-- Show scraped results --}}
    @if(isset($scrapedData) && count($scrapedData) > 0)       
            <form action="{{ route('parseImage.store') }}" method="POST">
                @csrf
                <div>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 60px; text-align:center;">
                                <input type="checkbox" id="select-all" style="width:20px;height:20px;">Select
                            </th>
                            <th style="width: 200px; text-align:center;">Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scrapedData as $item)
                            <tr>
                                <td style="text-align:center;">
                                    <input 
                                        type="checkbox" 
                                        name="selected_images[]" 
                                        value="{{ e(json_encode($item)) }}" 
                                        class="select-item"
                                        style="width:20px;height:20px;display:inline-block;appearance:auto;-webkit-appearance:checkbox;-moz-appearance:checkbox;"
                                    >
                                </td>                            
                                <td style="text-align:center;">
                                    <img src="{{ $item['image'] }}" 
                                        alt="preview" 
                                        style="max-width:150px;max-height:150px;object-fit:contain;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" class="btn btn-success mt-3">Save Selected</button>
            </form>
        </div>
    @endif
</div>

@endsection
