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
        <rack :tiles="rack" @rackTileMoved="handleTileMoved"></rack>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../mixins/CreateBoard.vue').default
    ],
    data() {
        return {
            board: this.createBoard(),
            rack: [],
        }
    },
    methods: {
        handleTileMoved(position) {
            // moved from board (original y position is less than 15)
            if (position.y < 15) {
                this.board[position.y][position.x].letter = null
                this.board[position.y][position.x].value = null
                this.updateBoard(null, null, position.x, position.y)
            }
            // moved from rack (original y position is 15)
            if (position.y == 15) {
                this.rack[position.x].letter = null
                this.rack[position.x].value = null
                console.log("moved")
                this.updateRack(null, null, position.x)
            }
        },
        loadRack() {
            axios
                .get('/rack/' + this.gameId)
                .then(response => {
                    this.rack = response.data
                })
                .catch(error => console.log(error))
        },
        loadBoard() {
            let board = []
            axios
                .get('/board/' + this.gameId)
                .then(response => {
                    board = response.data
                    this.board = this.createBoard()
                    for (let tile of board) {
                        this.board[tile.y][tile.x].letter = tile.letter
                        this.board[tile.y][tile.x].value = tile.value
                    }
                    console.log("loading")
                })
                .catch(error => console.log(error))
        }
    },
    mounted() {
        this.loadRack()
        this.loadBoard()
        // tile mdropped on board
        Echo.private('board')
            .listen('BoardUpdate', (data) => {
                console.log("BoardUpdate")
                this.loadBoard()
            });
        // tile moved to rack
        Echo.private('board-delete')
            .listen('BoardDelete', (tile) => {
                console.log("BoardDelete")
                this.board[tile.y][tile.x].letter = null
                this.board[tile.y][tile.x].value = null
            });
    }
}
</script>
