<template>
    <div :style="{backgroundColor: $themeColor}" class="shadow-sm" style="position:sticky;top:0px;z-index:2">
        <div class="container p-2 flex items-center justify-between text-white">
            <brand class="h5 col-6">
                <div class="flex items-center">
                    <img src="../../../../public/images/bag.png" class="me-2" alt="" style="width:19px;height:19px">
                    Vintage
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
