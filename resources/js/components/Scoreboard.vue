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
        <tr>
            <td>
                <input class="form-control score-input" v-model="scoreInput" v-on:keyup.enter="writeScore" type="number"/>
            </td>
            <td><input class="form-control score-input input-placeholder" type="number" /></td>
        </tr>
<!--        <tr>-->
<!--            <td></td>-->
<!--            <td></td>-->
<!--        </tr>-->
    </table>
    </div>
</template>

<script>
import {forEach} from "lodash";

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
                    this.playerName = response.data.playerName
                    this.opponentName = response.data.opponentName
                    this.playerScores = response.data.playerScores
                    this.opponentScores = response.data.opponentScores
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
