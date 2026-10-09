<template>
    <div class="editor-wrap banner-editor">
        <section class="editor-card">
            <div class="editor-intro"><span class="resource-symbol banner-symbol">▧</span><div><h2>{{ editing ? 'Update banner' : 'Banner details' }}</h2><p>Upload a wide image and choose where it appears.</p></div></div>
            <form @submit.prevent="submit">
                <div class="form-field"><label>Banner photo <b v-if="!editing">*</b></label><label class="banner-upload" :class="{filled:preview}"><input type="file" accept="image/jpeg,image/png,image/webp" @change="pickImage"/><img v-if="preview" :src="preview" alt="Banner preview"/><div v-else><span>↑</span><strong>Choose banner photo</strong><small>JPG, PNG or WEBP · Maximum 5 MB</small></div><i v-if="preview">Click image to replace</i></label><small v-if="form.errors.image" class="field-error">{{ form.errors.image }}</small><small v-else class="banner-hint">Recommended ratio: 16:9 or wider.</small></div>
                <div class="form-field order-field"><label>Display order <b>*</b></label><input v-model.number="form.display_order" type="number" min="0" step="1"/><small v-if="form.errors.display_order" class="field-error">{{ form.errors.display_order }}</small><small v-else>Lower numbers appear first in the API response.</small></div>
                <div class="form-actions"><Link :href="route('banners.index')" class="cancel-button">Cancel</Link><button v-if="editing" type="button" class="delete-button" :disabled="deleting" @click="remove">{{ deleting?'Deleting…':'Delete' }}</button><button class="save-button" :disabled="form.processing"><span v-if="form.processing" class="mini-spinner"></span>{{ form.processing?'Saving…':editing?'Save changes':'Create banner' }}</button></div>
            </form>
        </section>
        <aside class="form-aside"><h3>Banner ordering</h3><ul><li>Order 0 appears before order 1.</li><li>Banners with the same order use their creation order.</li><li>The public API always returns this sequence.</li></ul><div class="aside-note"><span>i</span><p><strong>Keep text readable</strong>Use high-quality landscape photos with important content near the centre.</p></div></aside>
    </div>
</template>
<script setup>
import{ref,computed}from'vue';import{useForm,router}from'@inertiajs/vue3';import{route}from'ziggy-js';import{useToast}from'vue-toastification';
const props=defineProps({banner:{type:Object,default:null},nextOrder:{type:Number,default:0}}),editing=computed(()=>!!props.banner?.id),preview=ref(props.banner?.image||null),deleting=ref(false),toast=useToast();
const form=useForm({image:null,display_order:props.banner?.display_order??props.nextOrder});
const pickImage=e=>{const file=e.target.files?.[0];if(!file)return;if(file.size>5*1024*1024){toast.error('Banner must be smaller than 5 MB');e.target.value='';return}form.image=file;preview.value=URL.createObjectURL(file)};
const submit=()=>{if(!editing.value&&!form.image){toast.info('Please choose a banner photo');return}if(editing.value){form.transform(data=>({...data,_method:'put'})).post(route('banners.update',props.banner.id),{forceFormData:true})}else form.post(route('banners.store'),{forceFormData:true})};
const remove=()=>{if(!window.confirm('Delete this banner permanently?'))return;deleting.value=true;router.delete(route('banners.destroy',props.banner.id),{onFinish:()=>deleting.value=false})};
</script>
