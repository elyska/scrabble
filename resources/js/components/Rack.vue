<template>
    <div class="rack">
        <div class="letters">
            <cell v-for="tile in tiles" :key="tile.x" :tile="tile"></cell>
        </div>
        <div class="bottom-rack"></div>
    </div>
</template>

<script>
export default {
    props: {
        tiles: Array
    },
    data() {
        return {

        }
    },
    computed: {
    },
    methods: {
        handleTileMoved(position) {
            this.tiles[position.x].letter = null
            this.tiles[position.x].value = null
        },
    },
    mounted() {
        console.log('Rack mounted.')
    }
}
</script>
