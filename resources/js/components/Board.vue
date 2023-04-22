<template>
    <div id="board">
        <div class="board-row" v-for="y in Array(15).keys()">
            <cell v-for="x in Array(15).keys()" :key="x+y" :x="x" :y="y"></cell>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        title: String,
    },
    data() {
        return {
            message: 'Hello Vue!'
        }
    },
    mounted() {
        console.log('Board mounted.')
    }
}
</script>
