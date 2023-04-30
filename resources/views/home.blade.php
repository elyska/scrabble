@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('new-game') }}" method="post">
        @csrf
        <p class="mb-3">
            <label for="opponent" class="form-label">Jméno spoluhráče</label>
            <input class="form-control" type="text" name="opponent">
            @error('opponent')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </p>

        <p class="mb-3">
            <label for="language" class="form-label">Jazyk</label>
            <select class="form-control"  name="language">
                <option value="CS">Čeština</option>
            </select>
        </p>

        <button class="btn btn-primary"  type="submit">Hrát</button>
    </form>
</div>
@endsection
