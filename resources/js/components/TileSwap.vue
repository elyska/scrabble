<template>
    <div>

        <div class="swap-drop-zone drop-zone"  id="my-tiles"
             @drop="onDropMyTiles($event)"
             @dragover.prevent
             @dragenter.prevent>
            <letter v-for="tile in tiles" :key="tile.x" :tile="tile"></letter>
        </div>


        <div class="swap-drop-zone drop-zone" id="tiles-to-swap" @drop="onDrop($event)"  @dragover="handleDragover($event)"
             @dragover.prevent
             @dragenter.prevent>

            <letter v-for="tile in tilesToSwap" :key="tile.x" :tile="tile"></letter>
        </div>
    </div>
</template>

<script>
import Letter from "./Letter.vue";

export default {
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
        handleDragover(e) {
            console.log("handleDragover")
            //if (this.tile.letter === null) e.preventDefault()
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

            // append
            // const swapContainer = document.querySelector('#my-tiles')
            // const mountNode = document.createElement('div')
            // mountNode.id = 'mount-node'
            // swapContainer.appendChild(mountNode)
            //
            // let draggedLetter = Vue.extend(Letter)
            // console.log(draggedLetter)
            // let letterToSwap = new draggedLetter({
            //     propsData: {
            //         tile: tile
            //     }
            // }).$mount('#mount-node')

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
