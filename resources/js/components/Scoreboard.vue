<template>
    <div class="container scoreboard-container">
    <table class="scoreboard">
        <tr>
            <th>{{ playerName }}</th>
            <th>{{ opponentName }}</th>
        </tr>
        <tr class="score-list">
            <td>
                <div class="score-list-container">
                    <p v-for="record in playerScores" :key="record.id">{{ record.score }}</p>
                </div>
            </td>
            <td>
                <div class="score-list-container">
                <p v-for="record in opponentScores" :key="record.id">{{ record.score }}</p>
                </div>
            </td>
        </tr>
        <tr v-if="finished">
            <td><strong>{{ playerTotal }}</strong></td>
            <td><strong>{{ opponentTotal }}</strong></td>
        </tr>
        <tr>
            <td>
                <input class="form-control score-input" v-model="scoreInput" v-on:keyup.enter="writeScore" type="number" :disabled='finished'/>
            </td>
            <td><button class="btn end-game-btn" v-on:click="controlModal" :disabled='finished'>Konec</button></td>
        </tr>
    </table>
        <!-- Modal - End game request -->
        <modal v-if="showModal" >
            <template v-slot:body>
                <h5>
                    Opravdu chcete ukončit hru?
                </h5>
                <p>Hra skončí, pokud protihráč bude souhlasit.</p>
            </template>
            <template v-slot:footer>
                <button class="btn end-game-btn" v-on:click="endGameRequest">Ano</button>
                <button class="btn game-button" v-on:click="controlModal">Ne</button>
            </template>
        </modal>
        <!-- Modal - End game confirm -->
        <modal v-if="showConfirmModal" >
            <template v-slot:body>
                <h5>
                    {{ opponentName }} žádá o ukončení hry
                </h5>
            </template>
            <template v-slot:footer>
                <button class="btn end-game-btn" v-on:click="endGameConfirm">Souhlasím</button>
                <button class="btn game-button" v-on:click="endGameReject">Pokračovat ve hře</button>
            </template>
        </modal>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../mixins/CreateBoard.vue').default
    ],
    data() {
        return {
            gameId: $cookies.get("gameId"),
            scoreInput: null,
            playerName: null,
            opponentName: null,
            playerScores: [],
            opponentScores: [],
            playerTotal: null,
            opponentTotal: null,
            showModal: false,
            showConfirmModal: false,
            finished: false
        }
    },
    methods: {
        scrollToBottom() {
            // always scroll down in the score table
            let scorelists = this.$el.querySelectorAll(".score-list-container");
            for(let list of scorelists) {
                setTimeout(() => {
                    list.scrollTop = list.scrollHeight;
                }, 500);
            }
        },
        loadScoreboard() {
            console.log("load scoreboard")
            console.log(this.gameId)

            axios
                .get('/scoreboard/' + this.gameId)
                .then(response => {
                    console.log(response.data)
                    this.playerName = response.data.playerName
                    this.opponentName = response.data.opponentName
                    this.playerScores = response.data.playerScores
                    this.opponentScores = response.data.opponentScores
                    this.playerTotal = response.data.playerTotal
                    this.opponentTotal = response.data.opponentTotal
                    this.finished = Boolean(response.data.finished)
                    this.scrollToBottom()
                })
                .catch(error => console.log(error))
        },
        writeScore() {
            axios
                .post('/score/' + this.gameId, {
                    score: this.scoreInput
                })
                .then(response => {
                    this.playerScores.push(response.data)
                    this.scoreInput = null
                })
                .catch(error => console.log(error))
            this.scrollToBottom()
        },
        endGameRequest() {
            axios
                .post('/end-game-request/' + this.gameId)
                .then(response => {
                    console.log(response)

                    // this.playerTotal = response.data.userScore
                    // this.opponentTotal = response.data.opponentScore

                    //this.quitDisabled = true;
                    this.controlModal();
                })
                .catch(error => console.log(error))
        },
        endGameConfirm() {
            axios
                .post('/end-game-confirm/' + this.gameId)
                .then(response => {
                    console.log(response)
                    this.playerTotal = response.data.userScore
                    this.opponentTotal = response.data.opponentScore
                    this.finished = true
                })
                .catch(error => console.log(error))
            this.showConfirmModal = false
        },
        endGameReject() {
            axios
                .post('/end-game-reject/' + this.gameId)
                .then(response => {
                    console.log(response)
                })
                .catch(error => console.log(error))
            this.showConfirmModal = false
        },
        controlModal() {
            this.showModal = !this.showModal
        },
        controlConfirmModal() {
            this.showConfirmModal = !this.showConfirmModal
        }
    },
    mounted() {
        this.loadScoreboard();

        Echo.private(`score.${this.gameId}`)
            .listen('ScoreWrite', (data) => {
                console.log("ScoreWrite")
                console.log(data)
                this.loadScoreboard()
            });

        Echo.private(`end-game-request.${this.gameId}`)
            .listen('EndGameRequest', (data) => {
                console.log("EndGameRequest")
                console.log(data)
                this.controlConfirmModal()
            });
        Echo.private(`end-game-result.${this.gameId}`)
            .listen('EndGameResult', (data) => {
                console.log("EndGameResult")
                console.log(data)
                this.playerTotal = data.userScore
                this.opponentTotal = data.opponentScore
                this.finished = data.accepted
            });
    }
}
</script>
