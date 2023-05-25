@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{ __("Swap letters") }}</h1>
            <div class="col">
                <form>
                    @csrf
                    <input type="hidden" name="letters">
                    <p>{{ __("Select the letters you want to swap") }}</p>
                    <div class="swap-letter-container">
                        @foreach($rack as $tile)
                            <div class="letter" >
                                <p>{{ $tile->letter }}</p>
                                @if($tile->value != 0)<p class="value">{{ $tile->value }}</p>@endif
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn generic-input">{{ __("Swap letters") }}</button>
                        <a href="/game/{{ $gameId }}" class="btn generic-input game-button">Back to the game</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section("js")
<script>
    const letterInput = document.querySelector("input[name='letters']")
console.log(document.cookie)
</script>
@endsection
