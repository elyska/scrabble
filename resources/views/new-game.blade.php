@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-5 col-lg-3">
                <h1 class="mb-4 text-center">{{ __('New Game') }}</h1>

                <form action="{{ route('create-new-game') }}" method="post">
                    @csrf
                    <p class="mb-4">
                        <input class="form-control" type="text" name="opponent" placeholder="{{ __('Opponent Name') }}">
                    @error('opponent')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                    </p>

                    <p class="mb-4">
                        <select class="form-control"  name="language">
                            <option value="CS" disabled selected>{{ __("Language") }}</option>
                            <option value="CS">{{ __("Czech") }}</option>
                        </select>
                    </p>

                    <button class="btn w-100" type="submit">{{ __("Create Game") }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
