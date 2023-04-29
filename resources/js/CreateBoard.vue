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
                // [
                // [
                //     {
                //         letter: "A",
                //         value: 1,
                //         x: 0,
                //         y: 0
                //     },
                //     {
                //         letter: null,
                //         value: null,
                //         x: 1,
                //         y: 0
                //     },
                // ],
                //     [
                //         {
                //             letter: null,
                //             value: null,
                //             x: 0,
                //             y: 1
                //         },
                //         {
                //             letter: "B",
                //             value: 3,
                //             x: 1,
                //             y: 1
                //         }
                //     ],
                // ]
        }
    }
}
</script>
