<template>
    <button class="btn btn-primary w-25 refill-button" @click="refill">Doplnit písmena</button>
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
