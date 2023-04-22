<template>
    <div class="letter">
        <p>{{letter}}</p>
        <p class="value">{{value}}</p>
    </div>
</template>

<script>
export default {
    props: {
        letter: String,
        value: Number
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
