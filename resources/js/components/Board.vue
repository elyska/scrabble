<template>
    <div class="row justify-content-center">
        <div id="board">
            <div class="board-row" v-for="row in board">
                <cell v-for="cell in row"
                      :key="cell.x + cell.y"
                      :tile="cell"
                      @tileMoved="handleTileMoved"
                >
                </cell>
            </div>
        </div>
        <rack :tiles="tiles" @rackTileMoved="handleTileMoved"></rack>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../CreateBoard.vue').default
    ],
    data() {
        return {
            board: this.createBoard(),
            tiles: [
                {
                    letter: "S",
                    value: 1,
                    x: 0,
                    y: 15
                },
                {
                    letter: null,
                    value: null,
                    x: 1,
                    y: 15
                }
            ]
        }
    },
    methods: {
        handleTileMoved(position) {
            // moved from board (original y position is less than 15)
            if (position.y < 15) {
                this.board[position.y][position.x].letter = null
                this.board[position.y][position.x].value = null
            }
            // moved from rack (original y position is 15)
            if (position.y == 15) {
                this.tiles[position.x].letter = null
                this.tiles[position.x].value = null
            }

        },
    },
    mounted() {
        console.log('Board mounted.')
    }
}
</script>
