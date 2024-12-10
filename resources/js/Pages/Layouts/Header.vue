<template>
    <div :style="{ backgroundColor: $themeColor }" class="shadow-sm" style="position:sticky;top:0px;z-index:2">
        <div class="container p-3 flex items-center justify-between text-white">
            <brand class="h5 col-6">
                <div>
                    Admin Dashboard
                </div>
            </brand>
            <div>
                <Search />
            </div>
        </div>
    </div>
</template>

<script setup>
import Search from './Search.vue';
</script>

<style></style>
