<template>

</template>

<script>
export default {
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
        }
    }
}
</script>
