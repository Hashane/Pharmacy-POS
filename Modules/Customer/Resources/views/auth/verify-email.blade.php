@extends('customer::layouts.app')

@section('title', 'Verify Email')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0"><i class="bi bi-envelope-check"></i> Verify Your Email</h4>
                    </div>
                    <div class="card-body text-center">
                        <i class="bi bi-envelope" style="font-size: 4rem; color: var(--primary-color);"></i>

                        <h5 class="mt-3">Thanks for signing up!</h5>

                        <p class="text-muted">
                            Before getting started, please verify your email address by clicking on the link
                            we just emailed to you. If you didn't receive the email, we will gladly send you another.
                        </p>

                        <form method="POST" action="{{ route('customer.verification.send') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Resend Verification Email
                            </button>
                        </form>

                        <div class="mt-3">
                            <form method="POST" action="{{ route('customer.logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection