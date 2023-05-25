<template>
    <div>
        <rack :tiles="rack"></rack>
    </div>
</template>

<script>
export default {
    mixins: [
        require('../mixins/CreateBoard.vue').default
    ],
    data() {
        return {
            rack: [],
        }
    },
    methods: {
        loadRack() {
            axios
                .get('/rack/' + this.gameId)
                .then(response => {
                    this.rack = response.data
                })
                .catch(error => console.log(error))
        },
    },
    mounted() {
        this.loadRack()
    }
}
</script>
