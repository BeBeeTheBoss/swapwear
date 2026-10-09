<template>
    <div class="admin-shell">
        <Navbar :open="sidebarOpen" @close="sidebarOpen = false" />
        <div v-if="sidebarOpen" class="sidebar-scrim" @click="sidebarOpen = false"></div>
        <div class="admin-main">
            <Header @toggle-menu="sidebarOpen = !sidebarOpen" />
            <main class="admin-content"><slot /></main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Navbar from './Navbar.vue';
import Header from './Header.vue';
const sidebarOpen = ref(false);
</script>
