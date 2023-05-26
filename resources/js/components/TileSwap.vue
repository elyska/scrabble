<template>
    <div>

        <div class="swap-drop-zone drop-zone"  id="my-tiles"
             @drop="onDropMyTiles($event)"
             @dragover.prevent
             @dragenter.prevent>
            <letter v-for="tile in tiles" :key="tile.x" :tile="tile"></letter>
        </div>


        <div class="swap-drop-zone drop-zone" id="tiles-to-swap"
             @drop="onDrop($event)"
             @dragover.prevent
             @dragenter.prevent>

            <letter v-for="tile in tilesToSwap" :key="tile.x" :tile="tile"></letter>
        </div>

        <div class="d-flex justify-content-between">
            <button @click="swap" type="submit" class="btn generic-input" :disabled="tilesToSwap.length === 0">{{ swapTranslation }}</button>
            <a :href="'/game/' + gameId" class="btn generic-input game-button">{{ backTranslation }}</a>
         </div>
    </div>
</template>

<script>

export default {
    props: ["swapTranslation", "backTranslation"],
    data() {
        return {
            gameId: $cookies.get("gameId"),
            tiles: [],
            tilesToSwap: []
        }
    },
    methods: {
        loadRack() {
            axios
                .get('/tiles-to-swap/' + this.gameId)
                .then(response => {
                    this.tiles = response.data
                    console.log( response.data)
                })
                .catch(error => console.log(error))
        },
        swap() {
            axios
                .post('/swap-tiles/' + this.gameId, {tilesToSwap: this.tilesToSwap, tiles: this.tiles})
                .then(response => {
                    console.log( response.data)
                    if (response.data.message) alert(response.data.message)
                    else window.location.href = '/game/' + this.gameId;
                })
                .catch(error => console.log(error))
        },
        onDrop(evt) {
            console.log("ondrop")

            // place tile in the new position
            const letter = evt.dataTransfer.getData('letter')
            const value = parseInt(evt.dataTransfer.getData('value'))
            const x = parseInt(evt.dataTransfer.getData('x'))
            const y = parseInt(evt.dataTransfer.getData('y'))

            const tile = {
                letter: letter,
                value: value,
                x: x,
                y: y
            }

            // remove tile
            for (let i = 0; i < this.tiles.length; i++) {
                if (this.tiles[i].x == x && this.tiles[i].y == y) {
                    this.tiles.splice(i, 1)
                    this.tilesToSwap.push(tile)
                    break
                }
            }
            console.log(this.tilesToSwap)
            console.log(this.tiles)
        },
        onDropMyTiles(evt) {
            console.log("onDropMyTiles")

            // place tile in the new position
            const letter = evt.dataTransfer.getData('letter')
            const value = parseInt(evt.dataTransfer.getData('value'))
            const x = parseInt(evt.dataTransfer.getData('x'))
            const y = parseInt(evt.dataTransfer.getData('y'))

            const tile = {
                letter: letter,
                value: value,
                x: x,
                y: y
            }

            // remove tile
            for (let i = 0; i < this.tilesToSwap.length; i++) {
                if (this.tilesToSwap[i].x == x && this.tilesToSwap[i].y == y) {
                    this.tilesToSwap.splice(i, 1)
                    this.tiles.push(tile)
                    break
                }
            }
            console.log(this.tilesToSwap)
            console.log(this.tiles)
        }
    },
    mounted() {
        this.loadRack()
    }
}
</script>
