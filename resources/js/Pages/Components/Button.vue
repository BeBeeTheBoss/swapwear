<template>
    <button :style="{backgroundColor: $themeColor}" class="btn text-white">
        <span class="me-2">
            {{ text }}
        </span>
        <font-awesome-icon icon="fa-solid fa-wrench" />
    </button>
</template>

<script setup>
const props = defineProps({
    text : String
})
</script>

<style>

</style>
