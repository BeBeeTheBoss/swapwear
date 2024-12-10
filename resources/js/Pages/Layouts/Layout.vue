<template>
    <v-container>
        <Header/>
        <div class="container">
            <slot/>
        </div>
        <Navbar v-if="currentUrl != '/dashboard'"/>
    </v-container>
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
