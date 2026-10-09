<template>
    <main class="login-page">
        <section class="login-story">
            <div class="login-brand"><span><img :src="logo" alt=""/></span><strong>SwapWear</strong></div>
            <div class="story-copy"><span class="story-tag">THE CIRCULAR MARKETPLACE</span><h1>Give great fashion<br/><em>another story.</em></h1><p>Manage the community making second-hand style the first choice.</p><div class="story-stats"><div><strong>Safer</strong><span>verified community</span></div><div><strong>Smarter</strong><span>circular shopping</span></div><div><strong>Better</strong><span>for the planet</span></div></div></div>
            <div class="story-footer"><span>© {{ new Date().getFullYear() }} SwapWear</span><span>Secure admin portal</span></div>
        </section>
        <section class="login-panel">
            <div class="mobile-brand"><span><img :src="logo" alt=""/></span><strong>SwapWear</strong></div>
            <form class="login-form" @submit.prevent="login"><div class="login-heading"><span>ADMIN PORTAL</span><h2>Welcome back</h2><p>Sign in to manage your SwapWear marketplace.</p></div>
                <div class="form-field"><label>Phone number</label><div class="login-input"><svg viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2.1Z"/></svg><input v-model="form.phone" autocomplete="username" placeholder="09 123 456 789"/></div><small v-if="form.errors.phone" class="field-error">{{ form.errors.phone }}</small></div>
                <div class="form-field"><div class="password-label"><label>Password</label><span>Forgot password?</span></div><div class="login-input"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg><input v-model="form.password" :type="showPassword?'text':'password'" autocomplete="current-password" placeholder="Enter your password"/><button type="button" @click="showPassword=!showPassword">{{ showPassword?'Hide':'Show' }}</button></div><small v-if="form.errors.password" class="field-error">{{ form.errors.password }}</small></div>
                <label class="remember-row"><input type="checkbox"/><span>Keep me signed in on this device</span></label><button class="login-submit" :disabled="form.processing"><span v-if="form.processing" class="mini-spinner"></span>{{ form.processing?'Signing in…':'Sign in to dashboard' }}<b v-if="!form.processing">→</b></button><div class="secure-note"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>Your access is protected with secure authentication.</div>
            </form>
        </section>
    </main>
</template>
<script setup>
import { ref, onMounted } from 'vue'; import { useForm, usePage } from '@inertiajs/vue3'; import { route } from 'ziggy-js'; import { useToast } from 'vue-toastification'; import logo from '../../../public/images/bag.png';
const showPassword=ref(false), toast=useToast(), page=usePage(); const form=useForm({phone:'',password:''});
const login=()=>{if(!form.phone||!form.password){toast.info('Enter your phone number and password');return} form.post(route('login'))};
onMounted(()=>{if(page.props.flash?.error)toast.error(page.props.flash.error)});
</script>
