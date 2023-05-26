@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h1>{{ __("Swap letters") }}</h1>
            <p class="white-text">{{ __("Remaining tiles: ")  . $remainingTiles }}</p>

            <div class="col">
                <tile-swap swap-translation="{{ __("Swap tiles") }}" back-translation="{{ __("Back to the game") }}" />
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
