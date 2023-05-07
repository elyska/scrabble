<template>
    <img src="/images/scrabble-bag.png" class="bag" @click="refill" />
</template>

<script>
export default {
    methods: {
        refill() {
            this.$emit('refill')
        }
    }
}
</script>
