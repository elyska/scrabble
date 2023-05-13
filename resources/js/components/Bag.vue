<template>
    <img src="/images/scrabble-bag.png" class="bag" @click="bagClick" />
</template>

<script>
export default {
    methods: {
        bagClick() {
            this.$emit('bagClick')
        }
    }
}
</script>
