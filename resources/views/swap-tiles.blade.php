@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{ __("Swap letters") }}</h1>
            <div class="col">
                <tile-swap />

{{--                <form method="post" action="/swap-tiles">--}}
{{--                    @csrf--}}
{{--                    <input type="hidden" name="letters" value="{}">--}}
{{--                    <p>{{ __("Select the letters you want to swap") }}</p>--}}
{{--                    <div class="swap-letter-container">--}}
{{--                        @foreach($rack as $tile)--}}
{{--                            <button type="button" class="letter btn">l</button>--}}
{{--                            <div class="" >--}}
{{--                                <p>{{ $tile->letter }}</p>--}}
{{--                                @if($tile->value != 0)<p class="value">{{ $tile->value }}</p>@endif--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}

{{--                    <div class="d-flex justify-content-between">--}}
{{--                        <button type="submit" class="btn generic-input">{{ __("Swap letters") }}</button>--}}
{{--                        <a href="/game/{{ $gameId }}" class="btn generic-input game-button">Back to the game</a>--}}
{{--                    </div>--}}
{{--                </form>--}}
            </div>
        </div>
    </div>
@endsection

@section("js")
<script>
    const letterInput = document.querySelector("input[name='letters']")
    const tiles = document.querySelectorAll(".letter")
    const tile1 = document.querySelectorAll(".letter")[0]

    // tile1.addEventListener("click", () => {
    //     tile1.classList.add("selected-tile");
    //     console.log(tile1)
    // })
    for (const tile of tiles) {
        console.log(tile)
        tile.addEventListener("click", () => {
            tile.classList.add("selected-tile");
            console.log(tile)
        })
    }


</script>
@endsection
