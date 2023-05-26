<template>

</template>

<script>
export default {
    data() {
        return {
        }
    },
    methods: {
        createBoard() {
            let board = []
            let row = []
            for (let y = 0; y < 15; y++) {
                for (let x = 0; x < 15; x++) {
                    row.push({
                        letter: null,
                        value: null,
                        x: x,
                        y: y
                    })
                }
                board.push(row)
                row = []
            }
            return board
        },
        updateRack(letter, value, x) {
            console.log("updateRack", letter, x)
            axios
                .post('/rack-update/' + this.gameId, {
                    letter: letter,
                    value: value,
                    x: x
                })
                .then(response => {
                    //console.log(response.data)
                })
                .catch(error => console.log(error))
        },
        updateBoard(letter, value, x, y) {
            axios
                .post('/board-update/' + this.gameId, {
                    letter: letter,
                    value: value,
                    x: x,
                    y: y
                })
                .then(response => {
                    return response.data
                })
                .catch(error => {
                    return false
                })
        }
    }
}
</script>
