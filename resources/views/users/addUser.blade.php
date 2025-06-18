@extends('layout.layout')
@php
    $title='Add User';
    $subTitle = 'Add User';
    $script = '<script>
                    // ================== Image Upload Js Start ===========================
                    function readURL(input) {
                        if (input.files && input.files[0]) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $("#imagePreview").css("background-image", "url(" + e.target.result + ")");
                                $("#imagePreview").hide();
                                $("#imagePreview").fadeIn(650);
                            }
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                    $("#imageUpload").change(function() {
                        readURL(this);
                    });
                    // ================== Image Upload Js End ===========================
             </script>';
@endphp

@section('content')

            <div class="card h-100 p-0 radius-12">
                <div class="card-body p-24">
                    <div class="row justify-content-center">
                        <div class="col-xxl-6 col-xl-8 col-lg-10">
                            <div class="card border">
                                <div class="card-body">
                                    <h6 class="text-md text-primary-light mb-16">Profile Image</h6>                                    
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <form action="{{ route('storeuser') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                    <!-- Upload Image Start -->
                                    <div class="mb-24 mt-16">
                                        <div class="avatar-upload">
                                            <div class="avatar-edit position-absolute bottom-0 end-0 me-24 mt-16 z-1 cursor-pointer">
                                                <input type='file' id="imageUpload" name="profile" accept=".png, .jpg, .jpeg" hidden>
                                                <label for="imageUpload" class="w-32-px h-32-px d-flex justify-content-center align-items-center bg-primary-50 text-primary-600 border border-primary-600 bg-hover-primary-100 text-lg rounded-circle">
                                                    <iconify-icon icon="solar:camera-outline" class="icon"></iconify-icon>
                                                </label>
                                            </div>
                                            <div class="avatar-preview">
                                                <div id="imagePreview"> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Upload Image End -->

                                    
                                    <div class="mb-20">
                                        <label for="username" class="form-label fw-semibold text-primary-light text-sm mb-8">Username <span class="text-danger-600">*</span></label>
                                        <input type="text" class="form-control radius-8 @error('username') is-invalid @enderror" id="username" name="username" placeholder="Enter username" value="{{ old('username') }}">
                                        @error('username')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-20">
                                        <label for="password" class="form-label fw-semibold text-primary-light text-sm mb-8">Password <span class="text-danger-600">*</span></label>
                                        <input type="text" class="form-control radius-8 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter password" value="{{ old('password') }}">
                                        @error('password')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- First Name -->
                                    <div class="mb-20">
                                        <label for="firstname" class="form-label fw-semibold text-primary-light text-sm mb-8">First Name <span class="text-danger-600">*</span></label>
                                        <input type="text" class="form-control radius-8 @error('firstname') is-invalid @enderror" id="firstname" name="firstname" placeholder="Enter first name" value="{{ old('firstname') }}">
                                        @error('firstname')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Last Name -->
                                    <div class="mb-20">
                                        <label for="lastname" class="form-label fw-semibold text-primary-light text-sm mb-8">Last Name <span class="text-danger-600">*</span></label>
                                        <input type="text" class="form-control radius-8 @error('lastname') is-invalid @enderror" id="lastname" name="lastname" placeholder="Enter last name" value="{{ old('lastname') }}">
                                        @error('lastname')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-20">
                                        <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span class="text-danger-600">*</span></label>
                                        <input type="email" class="form-control radius-8 @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}">
                                        @error('email')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Phone -->
                                    <div class="mb-20">
                                        <label for="phone" class="form-label fw-semibold text-primary-light text-sm mb-8">Phone <span class="text-danger-600">*</span></label>
                                        <input type="text" class="form-control radius-8 @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="text-danger text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <a href="{{ route('usersList') }}" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8">
                                            Cancel
                                        </a>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Save
                                    </button>
                                </div>
                            </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection

<script>
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 3000);
</script>