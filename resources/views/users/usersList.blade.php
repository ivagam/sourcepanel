@extends('layout.layout')

@php
    $title = 'Users Grid';
    $subTitle = 'Users Grid';
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
                    <h5 class="card-title mb-0">User</h5>
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
                        <th scope="col">Profile</th>
                        <th scope="col">User Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Phone</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
            @forelse ($users as $key => $user)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-10">
                            <div class="form-check style-check d-flex align-items-center">
                                <input class="form-check-input radius-4 border border-neutral-400" type="checkbox" name="checkbox">
                            </div>
                            {{ $key + 1 }}
                        </div>
                    </td>                                
                    <td>
                        <div class="d-flex align-items-center">
                            @if($user->profile)
                            <img src="{{ asset('public/' . $user->profile) }}" alt="Profile" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                            @else
                                <img src="{{ asset('public/' . ($user->profile ?? 'default.jpg')) }}" alt="Default" class="w-40-px h-40-px rounded-circle flex-shrink-0 me-12 overflow-hidden">
                            @endif                                        
                        </div>
                    </td>
                    <td>
                        <div class="flex-grow-1">
                            <span class="text-md mb-0 fw-normal text-secondary-light">{{ $user->username }}</span>
                        </div>
                    </td>
                    <td><span class="text-md mb-0 fw-normal text-secondary-light">{{ $user->email }}</span></td>
                    <td>{{ $user->firstname }}</td>
                    <td>{{ $user->lastname }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td class="text-center">
                        <span class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">Active</span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex align-items-center gap-10 justify-content-center">
                            <a href="{{ route('editUser', $user->id) }}">
                                <button type="button" class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                    <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                </button>
                            </a>
                            <form action="{{ route('deleteUser', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
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
                    <td colspan="9" class="text-center">No users found.</td>
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
