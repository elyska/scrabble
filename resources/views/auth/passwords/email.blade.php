@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-3">
            <h1 class="text-center mb-4">{{ __('Reset Password') }}</h1>

            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <p class="mb-3">
                    <input placeholder="{{ __('Email Address') }}" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </p>

                <p class="mb-3">
                    <button type="submit" class="btn w-100">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
