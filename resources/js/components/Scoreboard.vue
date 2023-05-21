<template>
    <div class="container scoreboard-container">
    <table class="scoreboard">
        <tr>
            <th>{{ playerName }}</th>
            <th>{{ opponentName }}</th>
        </tr>
        <tr class="score-list">
            <td>
                <p v-for="record in playerScores" :key="record.id">{{ record.score }}</p>
            </td>
            <td>
                <p v-for="record in opponentScores" :key="record.id">{{ record.score }}</p>
            </td>
        </tr>
        <tr>
            <td>
<!--                <form method="post" :action="'/score/' + gameId">-->
<!--                    -->
<!--                    <input class="form-control score-input" type="number" />-->
<!--                </form>-->
                <input class="form-control score-input" v-model="scoreInput" v-on:keyup.enter="writeScore" type="number"/>
            </td>
            <td><input class="form-control score-input input-placeholder" type="number" /></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
    </table>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../mixins/CreateBoard.vue').default
    ],
    data() {
        return {
            scoreInput: null,
            playerName: null,
            opponentName: null,
            playerScores: [],
            opponentScores: []
        }
    },
    methods: {
        loadScoreboard() {
            console.log("load scoreboard")
            console.log(this.gameId)

            axios
                .get('/scoreboard/' + this.gameId)
                .then(response => {
                    this.playerName = response.data.playerName
                    this.opponentName = response.data.opponentName
                    this.playerScores = response.data.playerScores
                    this.opponentScores = response.data.opponentScores
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
        },
    },
    mounted() {
        this.loadScoreboard();

        Echo.private(`score.${this.gameId}`)
            .listen('ScoreWrite', (data) => {
                console.log("ScoreWrite")
                console.log(data)
                this.loadScoreboard();
            });
    }
}
</script>
