<template>
    <div
        class="letter drag-el"
        draggable
        @dragstart="startDrag($event, tile)"
    >
        <p>{{ tile.letter }}</p>
        <p class="value">{{ tile.value }}</p>
    </div>
</template>

<script>
export default {
    props: {
        tile: Object
    },
    data() {
        return {
        }
    },
    computed: {
    },
    methods: {
        startDrag(evt, item) {
            evt.dataTransfer.dropEffect = 'move'
            evt.dataTransfer.effectAllowed = 'move'
            evt.dataTransfer.setData('x', item.x)
            evt.dataTransfer.setData('y', item.y)
            evt.dataTransfer.setData('letter', item.letter)
            evt.dataTransfer.setData('value', item.value)
        }
    },
    mounted() {
        console.log('Letter mounted.')
    }
}
</script>
