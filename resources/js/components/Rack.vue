<template>
    <div class="rack">
        <div class="letters">
            <cell v-for="tile in tiles" :key="tile.x" :tile="tile" @tileMoved="handleTileMoved"></cell>
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
            this.$emit('rackTileMoved', {x: position.x, y: position.y})
        },
    },
    mounted() {
        console.log('Rack mounted.')
    }
}
</script>
