@extends('layouts.app')

@section('content')
    <div class="container">
        <board swap-translation="{{ __("Swap tiles") }}" skip-translation="{{ __("Skip my turn") }}" remaining-translation="{{ __("Remaining tiles: ") }}"></board>
    </div>
@endsection
