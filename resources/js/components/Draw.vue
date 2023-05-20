<template>
    <div class="container">
        <div class="row justify-content-center">
            <section class="col-sm-12 col-md-5 player-box-1">
                <p class="player-name">
                    {{ player }}
                </p>
                <div>
                    <letter v-if="playerTile" :tile="playerTile"></letter>
                </div>
            </section>
            <section class="col-sm-12 col-md-2 vs">
                <p>
                    vs.
                </p>
            </section>
            <section class="col-sm-12 col-md-5 player-box-2">
                <p class="player-name">
                    {{ opponent }}
                </p>
                <div>
                    <letter v-if="opponentTile" :tile="opponentTile"></letter>
                </div>
            </section>
        </div>
        <div class="row justify-content-center">
            <div class="col bag-container">
                <bag @bagClick="handleDraw" data-bs-toggle="tooltip"  :title="tooltip"></bag>
            </div>
        </div>
        <!-- Modal -->
        <modal v-if="playerTile && opponentTile && playerTile.value !== opponentTile.value" >
            <template v-slot:body>
                <h5>
                    <span v-if="playerTile.value > opponentTile.value">{{ player }}</span>
                    <span v-else>{{ opponent }}</span>
                    {{ starts }}!
                </h5>
            </template>
            <template v-slot:footer>
                <a :href="'/game/' + gameId" class="btn btn-primary">{{ play }}</a>
            </template>
        </modal>
        <!-- Modal - same values-->
        <modal v-if="playerTile && opponentTile && playerTile.value === opponentTile.value" >
            <template v-slot:body>
                <h5>
                    Same score!
                </h5>
            </template>
            <template v-slot:footer>
                <a :href="'/draw/' + gameId" class="btn btn-primary">Try again</a>
            </template>
        </modal>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../mixins/CreateBoard.vue').default
    ],
    props: ["opponent", "player", "tooltip", "starts", "play"],
    data() {
        return {
            playerTile: null,
            opponentTile: null
        }
    },
    methods: {
        handleDraw() {
            console.log("draw")
                axios
                    .post('/draw/' + this.gameId)
                    .then(response => {
                        console.log(response.data)
                        if (this.playerTile === null) {
                            this.playerTile = response.data
                        }
                    })
                    .catch(error => console.log(error))
        },
        loadDrawnLetters() {
            axios
                .get('/load-drawn-letters/' + this.gameId)
                .then(response => {
                    console.log(response.data)
                    this.playerTile = response.data.userTile
                    this.opponentTile = response.data.opponentTile
                })
                .catch(error => console.log(error))
        }
    },
    mounted() {
        this.loadDrawnLetters()

        Echo.private(`draw.${this.gameId}`)
            .listen('Draw', (data) => {
                console.log("Draw")
                this.loadDrawnLetters()
            });
    }
}
</script>
