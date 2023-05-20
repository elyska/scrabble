@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-5 col-lg-3">
            <h1 class="mb-4 text-center">{{ __("Login") }}</h1>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <p class="mb-3">
                    <input placeholder="{{ __('Email Address') }}" id="email" type="email" class="form-control @error('email') is-invalid @enderror generic-input" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </p>

                <p class="mb-3">
                    <input placeholder="{{ __('Password') }}" id="password" type="password" class="form-control @error('password') is-invalid @enderror generic-input" name="password" required autocomplete="current-password">

                    @error('password')
                        <div class="alert alert-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </p>

                <p class="mb-3 text-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </p>

                <p class="mb-3">
                    <button type="submit" class="btn w-100 generic-input">
                        {{ __('Login') }}
                    </button>
                </p>
                @if (false && Route::has('password.request')) {{-- Password reset disabled--}}
                    <p class="mb-0 text-center">
                        <a class="btn btn-link link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </p>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
