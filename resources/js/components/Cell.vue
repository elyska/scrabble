<template>
    <div class="cell" :class="cellType">
        <letter v-if="x === 2 && y === 10 || x === 5 && y === 9" letter="A" :value="3"></letter>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
        x: Number,
        y: Number,
    },
    data() {
        return {
            message: 'Hello Vue!'
        }
    },
    computed: {
        cellType() {
            if ( (this.x === 0 || this.x === 7 || this.x === 14) && (this.y === 0 || this.y === 14) ||
                 (this.x === 0 || this.x === 14 ) && this.y === 7 ) return "tripple-word"

            if ( (this.x === 1 || this.x === 13) && (this.y === 1 || this.y === 13) ||
                 (this.x === 2 || this.x === 12) && (this.y === 2 || this.y === 12) ||
                 (this.x === 3 || this.x === 11) && (this.y === 3 || this.y === 11) ||
                 (this.x === 4 || this.x === 10) && (this.y === 4 || this.y === 10) ) return "double-word"

            if ( (this.x === 3 || this.x === 11) && (this.y === 0 || this.y === 14) ||
                 (this.x === 6 || this.x === 8) && (this.y === 2 || this.y === 12) ||
                 (this.x === 0 || this.x === 7 || this.x === 14) && (this.y === 3 || this.y === 11) ||
                 (this.x === 2 || this.x === 6 || this.x === 8 || this.x === 12) && (this.y === 6 || this.y === 8) ||
                 (this.x === 3 || this.x === 11) && (this.y === 7)) return "double-letter"

            if ( (this.x === 5 || this.x === 9) && (this.y === 1 || this.y === 13) ||
                 (this.x === 1 || this.x === 5 || this.x === 9 || this.x === 13) && (this.y === 5 || this.y === 9)
               ) return "tripple-letter"

            if ( this.x === 7 && this.y === 7 ) return "star"

            return ""
        }
    },
    mounted() {
        console.log('Cell mounted.')
        console.log(this.x)
        console.log(this.y)
    }
}
</script>
