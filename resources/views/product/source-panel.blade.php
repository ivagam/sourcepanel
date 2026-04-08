@extends('layout.layout')
<style>
.image-row {
    display: grid;
    grid-template-columns: repeat(9, 1fr); /* exactly 10 columns */
    gap: 12px;
}

.image-col {
    width: 100%;
}
</style>
@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Source Panel (Images & Videos)</h4>

    <form method="POST" action="{{ route('createFromSource') }}">
        @csrf
        <div class="text-end mt-3">
            <button class="btn btn-primary">
                Save
            </button>
        </div>
        <div class="image-row">
    @foreach($files as $index => $file)
        @php
            $url = env('SOURCE_PANEL_IMAGE_URL_NEW') . $file['name'];
            $checkboxId = 'file_' . $index;
        @endphp

        <div class="image-col">
            <input type="checkbox"
                   id="{{ $checkboxId }}"
                   name="files[]"
                   value="{{ $file['name'] }}"
                   class="d-none file-checkbox">

            <label for="{{ $checkboxId }}"
                   class="card p-2 shadow-sm file-card"
                   style="cursor:pointer">

                @if(in_array($file['ext'], ['jpg','jpeg','png','gif','webp']))
                    <img src="{{ $url }}"
                         class="img-fluid rounded"
                         style="height:120px;object-fit:cover;">
                @elseif(in_array($file['ext'], ['mp4','mov','avi','webm']))
                    <video src="{{ $url }}"
                           preload="none"
                           style="width:100%;height:120px;object-fit:cover;">
                    </video>
                @endif
            </label>
        </div>
    @endforeach
</div>


        {{-- Pagination --}}
        <div class="d-flex justify-content-between mt-4">
            @if($page > 1)
                <a href="{{ route('sourcePanel', ['page' => $page - 1]) }}"
                   class="btn btn-secondary">
                    ⬅ Previous
                </a>
            @else
                <span></span>
            @endif

            @if($hasMore)
                <a href="{{ route('sourcePanel', ['page' => $page + 1]) }}"
                   class="btn btn-secondary">
                    Next ➡
                </a>
            @endif
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary">
                Save
            </button>
        </div>

    </form>

</div>

{{-- Selection Highlight --}}
<style>
    .file-checkbox:checked + .file-card {
        border: 3px solid #0d6efd;
        box-shadow: 0 0 10px rgba(13,110,253,.6);
    }
</style>

@endsection
