<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">

<x-head />

<body>

    <section class="auth forgot-password-page bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <img src="{{ asset('assets/images/forgot-pass-img.png') }}" alt="">
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <div>
                    <h4 class="mb-12">Forgot Password</h4>
                    <p class="mb-32 text-secondary-light text-lg">Enter your email</p>
                </div>

                <form action="{{ route('forgotpassword') }}" method="POST">
                        @csrf

                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email" class="form-control h-56-px bg-neutral-50 radius-12" placeholder="Enter Email">
                       
                    </div>
                      @if ($errors->has('email'))
                                <div class="text-danger">{{ $errors->first('email') }}</div>
                            @endif
                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Submit</button>

                    <div class="text-center">
                    <a  href="{{ route('signin') }}" class="text-primary-600 fw-bold mt-24">Back to Sign In</a>
                    </div>

                    <div class="mt-120 text-center text-sm">
                        <p class="mb-0">Already have an account? <a  href="{{ route('signin') }}" class="text-primary-600 fw-semibold">Sign In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-body p-40 text-center">
                    <div class="mb-32">
                        <img src="{{ asset('assets/images/auth/envelop-icon.png') }}" alt="">
                    </div>
                    <h6 class="mb-12">Verify your Email</h6>
                    <p class="text-secondary-light text-sm mb-0">Thank you, check your email for instructions to reset your password</p>
                    <button type="button" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32">Skip</button>
                    <div class="mt-32 text-sm">
                        <p class="mb-0">Don’t receive an email? <a  href="" class="text-primary-600 fw-semibold">Resend</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<x-script/>

</body>

</html>