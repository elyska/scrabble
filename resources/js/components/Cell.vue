<template>
    <div class="cell drop-zone" :class="cellType" @drop="onDrop($event)" @dragover="handleDragover($event)" @dragenter.prevent>
        <letter v-if="tile.letter" :tile="tile"></letter>
    </div>
</template>

<script>
export default {
    props: {
        tile: Object
    },
    data() {
        return {

        }
    },
    computed: {
        cellType() {
            if ( (this.tile.x === 0 || this.tile.x === 7 || this.tile.x === 14) && (this.tile.y === 0 || this.tile.y === 14) ||
                 (this.tile.x === 0 || this.tile.x === 14 ) && this.tile.y === 7 ) return "tripple-word"

            if ( (this.tile.x === 1 || this.tile.x === 13) && (this.tile.y === 1 || this.tile.y === 13) ||
                 (this.tile.x === 2 || this.tile.x === 12) && (this.tile.y === 2 || this.tile.y === 12) ||
                 (this.tile.x === 3 || this.tile.x === 11) && (this.tile.y === 3 || this.tile.y === 11) ||
                 (this.tile.x === 4 || this.tile.x === 10) && (this.tile.y === 4 || this.tile.y === 10) ) return "double-word"

            if ( (this.tile.x === 3 || this.tile.x === 11) && (this.tile.y === 0 || this.tile.y === 14) ||
                 (this.tile.x === 6 || this.tile.x === 8) && (this.tile.y === 2 || this.tile.y === 12) ||
                 (this.tile.x === 0 || this.tile.x === 7 || this.tile.x === 14) && (this.tile.y === 3 || this.tile.y === 11) ||
                 (this.tile.x === 2 || this.tile.x === 6 || this.tile.x === 8 || this.tile.x === 12) && (this.tile.y === 6 || this.tile.y === 8) ||
                 (this.tile.x === 3 || this.tile.x === 11) && (this.tile.y === 7)) return "double-letter"

            if ( (this.tile.x === 5 || this.tile.x === 9) && (this.tile.y === 1 || this.tile.y === 13) ||
                 (this.tile.x === 1 || this.tile.x === 5 || this.tile.x === 9 || this.tile.x === 13) && (this.tile.y === 5 || this.tile.y === 9)
               ) return "tripple-letter"

            if ( this.tile.x === 7 && this.tile.y === 7 ) return "star"

            return ""
        }
    },
    methods: {
        onDrop(evt) {
            // check if field is empty
            if (this.tile.letter !== null) return

            // place tile in the new position
            const letter = evt.dataTransfer.getData('letter')
            const value = evt.dataTransfer.getData('value')
            this.tile.letter = letter;
            this.tile.value = value;

            // update board
            const x = evt.dataTransfer.getData('x')
            const y = evt.dataTransfer.getData('y')
            this.$emit('tileMoved', {x: x, y: y})
        },
        handleDragover(e) {
            if (this.tile.letter === null) e.preventDefault()
        }
    },
    mounted() {
        console.log('Cell mounted.')
    }
}
</script>
