<template>
    <button :style="{ backgroundColor: $themeColor }" class="btn text-white">
        <div v-if="loading" style="width:25px;height:25px" class="spinner-border text-white" role="status">
        </div>
        <div v-else>
            <span class="me-2">
                {{ text }}
            </span>
            <font-awesome-icon :icon="icon" />
        </div>
    </button>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    text: String,
    icon: String,
    loading : Boolean,
})

</script>

<style></style>
