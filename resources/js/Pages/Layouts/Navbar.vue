<template>
    <aside class="sidebar" :class="{ 'sidebar-open': open }">
        <div class="brand-row">
            <div class="brand-mark"><img :src="logoImage" alt="" /></div>
            <div><strong>SwapWear</strong><span>Admin workspace</span></div>
            <button class="sidebar-close" aria-label="Close navigation" @click="$emit('close')">×</button>
        </div>
        <nav class="sidebar-nav">
            <p>Workspace</p>
            <Link :href="route('dashboard')" :class="{ active: route().current('dashboard') }"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/></svg>Overview</Link>
            <Link :href="route('resources')" :class="{ active: isResources }"><svg viewBox="0 0 24 24"><path d="M4 6.5 12 3l8 3.5-8 3.5-8-3.5Z"/><path d="m4 11 8 3.5 8-3.5M4 15.5 12 19l8-3.5"/></svg>Resources</Link>
            <Link :href="route('banners.index')" :class="{ active: route().current('banners.*') }"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 15-5-4L5 20"/></svg>Banners</Link>
            <Link :href="route('users.index')" :class="{ active: route().current('users.*') }"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>Users</Link>
            <Link :href="route('verification-requests.index')" :class="{ active: route().current('verification-requests.*') }"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>Verifications</Link>
            <Link :href="route('orders.index')" :class="{ active: route().current('orders.*') }"><svg viewBox="0 0 24 24"><path d="M6 2 3 6v14h18V6l-3-4H6ZM3 6h18M9 10a3 3 0 0 0 6 0"/></svg>Orders</Link>
            <Link :href="route('selling-products.index')" :class="{ active: route().current('selling-products.*') }"><svg viewBox="0 0 24 24"><path d="M6 2 3 6v14h18V6l-3-4H6ZM3 6h18"/><path d="m9 12 2 2 4-4"/></svg>Products</Link>
            <Link :href="route('payments.index')" :class="{ active: route().current('payments.*') }"><svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>Payments</Link>
        </nav>
        <div class="sidebar-help"><div>?</div><strong>Need help?</strong><span>View documentation</span></div>
        <div class="sidebar-footer">SwapWear Admin <span>v1.0</span></div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { route } from 'ziggy-js';
import logoImage from '../../../../public/images/bag.png';
defineProps({ open: Boolean });
defineEmits(['close']);
const isResources = computed(() => route().current('resources') || route().current('main-categories.*') || route().current('sub-categories.*'));
</script>
