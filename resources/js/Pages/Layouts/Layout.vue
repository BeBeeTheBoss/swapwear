<template>
    <div>
        <Header/>
        <div class="container">
            <slot/>
        </div>
        <Navbar/>
    </div>
</template>

<script setup>
import Navbar from './Navbar.vue';
import Header from './Header.vue';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const currentUrl = ref(page.url);

</script>

<style></style>
