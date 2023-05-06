@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ __('New Game') }}</h1>
    <form action="{{ route('new-game') }}" method="post">
        @csrf
        <p class="mb-3">
            <label for="opponent" class="form-label">{{ __('Opponent Name') }}</label>
            <input class="form-control" type="text" name="opponent">
            @error('opponent')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </p>

        <p class="mb-3">
            <label for="language" class="form-label">{{ __('Language') }}</label>
            <select class="form-control"  name="language">
                <option value="CS">{{ __("Czech") }}</option>
            </select>
        </p>

        <button class="btn btn-primary" type="submit">{{ __("Create Game") }}</button>
    </form>
</div>
@endsection
