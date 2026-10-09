<template>
    <Layout>
        <section class="dashboard-heading">
            <div><p>OVERVIEW</p><h1>Good {{ greeting }}, {{ firstName }} <span>👋</span></h1><div>Here’s what’s happening with your marketplace today.</div></div>
            <div class="heading-actions"><button class="secondary-button"><svg viewBox="0 0 24 24"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>Export report</button><Link :href="route('main-categories.create')" class="primary-button">＋ Add category</Link></div>
        </section>

        <section class="stats-grid">
            <article v-for="(stat, index) in stats" :key="stat.label" class="stat-card">
                <div class="stat-top"><span class="stat-icon" :class="`icon-${index}`"><svg v-if="index===0" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg><svg v-else-if="index===1" viewBox="0 0 24 24"><path d="M6 2 3 6v14h18V6l-3-4H6ZM3 6h18M9 10a3 3 0 0 0 6 0"/></svg><svg v-else-if="index===2" viewBox="0 0 24 24"><circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M3 4h2l2.5 11h11l2-7H6"/></svg><svg v-else viewBox="0 0 24 24"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span><span class="trend" :class="{ down: stat.change < 0 }">{{ stat.change >= 0 ? '↗' : '↘' }} {{ Math.abs(stat.change) }}%</span></div>
                <strong>{{ formatValue(stat) }}</strong><p>{{ stat.label }}</p><small>Compared with last month</small>
            </article>
        </section>

        <section class="dashboard-grid">
            <article class="panel activity-panel">
                <div class="panel-title"><div><h2>Order activity</h2><p>Orders received in the last 7 days</p></div><span class="period-pill">Last 7 days</span></div>
                <div class="chart-wrap">
                    <div class="chart-scale"><span>{{ chartMax }}</span><span>{{ Math.round(chartMax / 2) }}</span><span>0</span></div>
                    <div class="bar-chart">
                        <div v-for="day in orderActivity" :key="day.date" class="bar-column" :title="`${day.date}: ${day.orders} orders`"><div class="bar-track"><div class="chart-bar" :style="{ height: `${Math.max(day.orders ? 12 : 2, (day.orders / chartMax) * 100)}%` }"><b>{{ day.orders || '' }}</b></div></div><span>{{ day.label }}</span></div>
                    </div>
                </div>
            </article>

            <article class="panel quick-panel">
                <div class="panel-title"><div><h2>Needs attention</h2><p>Your marketplace queue</p></div></div>
                <div class="attention-list">
                    <div><span class="attention-icon amber">!</span><p><strong>{{ summary.pendingOrders }} pending orders</strong><small>Waiting for an update</small></p><b>›</b></div>
                    <div><span class="attention-icon blue">⌁</span><p><strong>{{ summary.pendingProducts }} inactive listings</strong><small>Review product availability</small></p><b>›</b></div>
                    <Link :href="route('verification-requests.index')"><span class="attention-icon purple-card">✓</span><p><strong>{{ summary.pendingVerifications }} verification requests</strong><small>Identity reviews waiting for approval</small></p><b>›</b></Link>
                    <div><span class="attention-icon green">✓</span><p><strong>{{ summary.mainCategories }} main categories</strong><small>{{ summary.subCategories }} subcategories configured</small></p><b>›</b></div>
                </div>
                <Link :href="route('resources')" class="panel-link">Manage resources <span>→</span></Link>
            </article>
        </section>

        <section class="dashboard-grid lower-grid">
            <article class="panel orders-panel">
                <div class="panel-title"><div><h2>Recent orders</h2><p>Latest marketplace transactions</p></div><button class="text-button">View all →</button></div>
                <div v-if="recentOrders.length" class="table-scroll"><table><thead><tr><th>Order</th><th>Customer</th><th>Product</th><th>Total</th><th>Status</th></tr></thead><tbody><tr v-for="order in recentOrders" :key="order.id"><td><strong>#{{ order.code }}</strong><small>{{ order.date }}</small></td><td><span class="customer-avatar">{{ order.customer.charAt(0) }}</span>{{ order.customer }}</td><td>{{ order.product }}</td><td><strong>{{ money(order.total) }}</strong></td><td><span class="status-pill" :class="statusClass(order.status)">{{ readableStatus(order.status) }}</span></td></tr></tbody></table></div>
                <div v-else class="empty-state"><div>↗</div><strong>No orders yet</strong><p>New marketplace orders will appear here.</p></div>
            </article>
            <article class="panel category-panel">
                <div class="panel-title"><div><h2>Top categories</h2><p>By number of subcategories</p></div></div>
                <div v-if="categories.length" class="category-list"><div v-for="(category,index) in categories" :key="category.id"><span>{{ index + 1 }}</span><p><strong>{{ category.name }}</strong><small>{{ category.count }} subcategories</small></p><div class="progress"><i :style="{ width: `${(category.count / categoryMax) * 100}%` }"></i></div></div></div>
                <div v-else class="empty-state compact"><strong>No categories yet</strong><p>Create your first category to get started.</p></div>
                <Link :href="route('main-categories.get')" class="panel-link">View categories <span>→</span></Link>
            </article>
        </section>
    </Layout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Layout from './Layouts/Layout.vue';

const props = defineProps({ stats: { type: Array, default: () => [] }, orderActivity: { type: Array, default: () => [] }, recentOrders: { type: Array, default: () => [] }, categories: { type: Array, default: () => [] }, summary: { type: Object, default: () => ({}) } });
const firstName = computed(() => usePage().props.auth?.user?.name?.split(' ')[0] || 'Admin');
const greeting = computed(() => { const h = new Date().getHours(); return h < 12 ? 'morning' : h < 18 ? 'afternoon' : 'evening'; });
const chartMax = computed(() => Math.max(5, ...props.orderActivity.map(day => day.orders)));
const categoryMax = computed(() => Math.max(1, ...props.categories.map(category => category.count)));
const money = value => new Intl.NumberFormat('en-US', { style: 'currency', currency: 'MMK', maximumFractionDigits: 0 }).format(value).replace('MMK', 'Ks');
const formatValue = stat => stat.format === 'currency' ? money(stat.value) : new Intl.NumberFormat().format(stat.value);
const readableStatus = status => (status || 'pending').replaceAll('-', ' ').replace(/\b\w/g, letter => letter.toUpperCase());
const statusClass = status => status?.toLowerCase().includes('reject') ? 'status-red' : status?.toLowerCase().includes('accept') || ['delivered','received'].includes(status?.toLowerCase()) ? 'status-green' : 'status-amber';
</script>
