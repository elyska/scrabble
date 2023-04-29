<template>
    <div class="rack">
        <div class="letters" @drop="onDrop($event)" @dragover.prevent @dragenter.prevent>
            <letter :tile="{ letter: 'C', value: 5 }"></letter>
            <letter :tile="{ letter: 'A', value: 4 }"></letter>
            <letter :tile="{ letter: 'N', value: 1 }"></letter>
            <letter :tile="{ letter: 'D', value: 1 }"></letter>
            <letter :tile="{ letter: 'Á', value: 2 }"></letter>
            <letter :tile="{ letter: 'T', value: 10 }"></letter>
            <letter :tile="{ letter: '', value: '' }"></letter>
        </div>
        <div class="bottom-rack"></div>
    </div>
</template>

<script>
export default {
    props: {
    },
    data() {
        return {
        }
    },
    computed: {
    },
    methods: {
    },
    mounted() {
        console.log('Rack mounted.')
    }
}
</script>
