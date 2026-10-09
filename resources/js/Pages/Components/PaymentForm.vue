<template>
    <div class="editor-wrap payment-editor">
        <section class="editor-card">
            <div class="editor-intro"><span class="resource-symbol payment-symbol">$</span><div><h2>{{ editing ? 'Update payment method' : 'Payment method details' }}</h2><p>Configure how this payment option appears to customers.</p></div></div>
            <form @submit.prevent="submit">
                <div class="form-field"><label>Display name <b>*</b></label><input v-model.trim="form.name" type="text" maxlength="100" placeholder="e.g. KBZPay"/><div class="field-meta"><small v-if="form.errors.name" class="field-error">{{ form.errors.name }}</small><small v-else>The name shown during checkout.</small><span>{{ form.name.length }}/100</span></div></div>
                <div class="status-control"><div><strong>Available for payments</strong><p>Customers can select this method for supported listings.</p></div><label class="toggle"><input v-model="form.is_active" type="checkbox"/><span></span></label></div>
                <div class="payment-preview"><span>Customer preview</span><div><i>{{ initials }}</i><p><strong>{{ form.name || 'Payment method' }}</strong><small>{{ form.is_active ? 'Available at checkout' : 'Currently unavailable' }}</small></p><b :class="{inactive:!form.is_active}">{{ form.is_active ? 'Active' : 'Inactive' }}</b></div></div>
                <div class="form-actions"><Link :href="route('payments.index')" class="cancel-button">Cancel</Link><button v-if="editing" type="button" class="delete-button" :disabled="deleting" @click="remove">{{ deleting?'Deleting…':'Delete' }}</button><button class="save-button" :disabled="form.processing"><span v-if="form.processing" class="mini-spinner"></span>{{ form.processing?'Saving…':editing?'Save changes':'Create method' }}</button></div>
            </form>
        </section>
        <aside class="form-aside"><h3>Payment availability</h3><ul><li>Active methods are marked available to the mobile app.</li><li>Listings can associate with one or more methods.</li><li>Disabling a method preserves existing order records.</li></ul><div class="aside-note"><span>i</span><p><strong>Before deleting</strong>Deleting removes its listing associations. Existing orders keep their history but no longer reference the method.</p></div></aside>
    </div>
</template>
<script setup>
import {computed,ref} from 'vue';import {useForm,router} from '@inertiajs/vue3';import {route} from 'ziggy-js';
const props=defineProps({payment:{type:Object,default:null}}),editing=computed(()=>!!props.payment?.id),deleting=ref(false);
const form=useForm({name:props.payment?.name||'',is_active:props.payment?.is_active??true});
const initials=computed(()=>(form.name||'PM').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase());
const submit=()=>{if(editing.value)form.put(route('payments.update',props.payment.id));else form.post(route('payments.store'))};
const remove=()=>{if(!window.confirm(`Delete “${form.name}”? Listing associations will also be removed.`))return;deleting.value=true;router.delete(route('payments.destroy',props.payment.id),{onFinish:()=>deleting.value=false})};
</script>
