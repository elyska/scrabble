<template>
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
</template>

<script>
//const createBoard = require('../CreateBoard.vue').default
import createBoard from "../CreateBoard.vue";

export default {
    mixins: [
        require('../CreateBoard.vue').default
    ],
    data() {
        return {
            board: this.createBoard()
        }
    },
    methods: {
        handleTileMoved(position) {
            this.board[position.y][position.x].letter = null
            this.board[position.y][position.x].value = null
        }
    },
    mounted() {
        console.log('Board mounted.')
    }
}
</script>
