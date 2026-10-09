<template>
    <header class="topbar">
        <button class="icon-button menu-button" aria-label="Open navigation" @click="$emit('toggle-menu')"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16" /></svg></button>
        <div class="topbar-search">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
            <input type="search" placeholder="Search dashboard..." aria-label="Search dashboard" />
            <span>⌘ K</span>
        </div>
        <div class="topbar-actions">
            <button class="icon-button notification-button" aria-label="Notifications"><svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg><i></i></button>
            <div class="profile-block"><div class="profile-avatar">{{ initials }}</div><div><strong>{{ user?.name || 'Administrator' }}</strong><small>Administrator</small></div></div>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
defineEmits(['toggle-menu']);
const user = computed(() => usePage().props.auth?.user);
const initials = computed(() => (user.value?.name || 'Admin').split(' ').map(word => word[0]).slice(0, 2).join('').toUpperCase());
</script>
