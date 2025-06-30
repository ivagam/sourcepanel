@extends('layout.layout')

@php
    $title = 'Media Management';
    $subTitle = 'Add Media';
    $script = '<script>
                    let table = new DataTable("#dataTable");
               </script>';
@endphp

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

            <div class="card basic-data-table">    
                    <div class="card-header">
                        <h5 class="card-title mb-0">Media List</h5>
                    </div>
                    <div class="card-body">                        
                        <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                            <thead>
                                <tr>
                                    <th class="text-center">S.L</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">File</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mediaFiles as $key => $mediaItem)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-10">                                    
                                                {{ $key + 1 }}
                                            </div>
                                        </td>
                                        <td>{{ $mediaItem->category ? $mediaItem->category->category_name : '-' }}</td>
                                        <td>
                                            @php
                                            $ext = strtolower(pathinfo($mediaItem->file_path, PATHINFO_EXTENSION));
                                            $fullPath = asset('public/' . $mediaItem->file_path); // Fix here
                                        @endphp

                                            @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                <a href="{{ $fullPath }}" target="_blank">
                                                    <img src="{{ $fullPath }}" alt="{{ basename($mediaItem->file_path) }}" style="max-width: 100px; height: auto;">
                                                </a>
                                            @elseif($ext === 'pdf')
                                                <a href="{{ $fullPath }}" target="_blank">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF" style="width: 40px;">
                                                    <br>
                                                    View PDF
                                                </a>
                                            @elseif($ext === 'mp4')
                                                <video width="120" controls>
                                                    <source src="{{ $fullPath }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @else
                                                <a href="{{ $fullPath }}" target="_blank">Download File</a>
                                            @endif
                                        </td>
                                        <td>{{ $mediaItem->file_type }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('deletemedia', $mediaItem->media_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this media?');">
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
                                        <td colspan="5" class="text-center">No media found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>                          
                    </div>
            </div>
               

@endsection

<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>
