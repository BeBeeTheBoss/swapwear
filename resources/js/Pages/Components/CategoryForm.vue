<template>
    <div class="editor-wrap">
        <section class="editor-card">
            <div class="editor-intro"><span class="resource-symbol">{{ isSubcategory ? 'S' : 'M' }}</span><div><h2>{{ mode === 'edit' ? 'Update' : 'Create' }} {{ isSubcategory ? 'subcategory' : 'main category' }}</h2><p>Use a clear name and a simple, recognisable icon.</p></div></div>
            <form @submit.prevent="submit">
                <div v-if="isSubcategory" class="form-field"><label>Parent category <b>*</b></label><select v-model="form.main_category_id"><option :value="null" disabled>Select a main category</option><option v-for="item in categoryOptions" :key="item.id" :value="item.id">{{ item.name }}</option></select><small v-if="form.errors.main_category_id" class="field-error">{{ form.errors.main_category_id }}</small></div>
                <div class="form-field"><label>Category name <b>*</b></label><input v-model.trim="form.name" type="text" placeholder="e.g. Vintage jackets" maxlength="80"/><div class="field-meta"><small v-if="form.errors.name" class="field-error">{{ form.errors.name }}</small><small v-else>Choose a short, descriptive name.</small><span>{{ form.name.length }}/80</span></div></div>
                <div class="form-field"><label>Category icon <b>*</b></label><label class="upload-zone" :class="{ 'has-preview': preview }"><input type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" @change="pickFile"/><img v-if="preview" :src="preview" alt="Icon preview"/><span v-else class="upload-icon">↑</span><div><strong>{{ preview ? 'Change icon' : 'Upload an icon' }}</strong><small>PNG, JPG, WEBP or SVG · Max 2 MB</small></div></label><small v-if="form.errors.icon" class="field-error">{{ form.errors.icon }}</small></div>
                <div class="form-actions"><Link :href="backRoute" class="cancel-button">Cancel</Link><button v-if="mode === 'edit'" type="button" class="delete-button" @click="remove" :disabled="deleting">{{ deleting ? 'Deleting…' : 'Delete' }}</button><button class="save-button" :disabled="form.processing"><span v-if="form.processing" class="mini-spinner"></span>{{ form.processing ? 'Saving…' : mode === 'edit' ? 'Save changes' : 'Create category' }}</button></div>
            </form>
        </section>
        <aside class="form-aside"><h3>Icon guidelines</h3><ul><li>Use a square image for the best result.</li><li>Keep the artwork simple and centred.</li><li>A transparent background works best.</li></ul><div class="aside-note"><span>i</span><p><strong>Visible in the app</strong>Your customers will see this name and icon while browsing products.</p></div></aside>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useToast } from 'vue-toastification';
const props = defineProps({ type:{type:String,default:'main'}, mode:{type:String,default:'create'}, item:{type:Object,default:null}, categories:{type:[Array,Object],default:()=>[]} });
const isSubcategory=computed(()=>props.type==='sub');
const categoryOptions=computed(()=>props.categories?.data || props.categories || []);
const data=computed(()=>props.item?.data || props.item || {});
const preview=ref(props.mode==='edit' ? data.value.icon : null); const deleting=ref(false); const toast=useToast();
const form=useForm({ id:data.value.id || null, main_category_id:data.value.main_category_id || null, name:data.value.name || '', icon:props.mode==='edit' ? data.value.icon : null });
const backRoute=computed(()=>route(isSubcategory.value?'sub-categories.get':'main-categories.get'));
const pickFile=e=>{const file=e.target.files?.[0]; if(!file)return; if(file.size>2*1024*1024){toast.error('Icon must be smaller than 2 MB');return} form.icon=file; preview.value=URL.createObjectURL(file)};
const submit=()=>{if(!form.name || !form.icon || (isSubcategory.value&&!form.main_category_id)){toast.info('Please complete all required fields');return} form.post(route(`${isSubcategory.value?'sub-categories':'main-categories'}.${props.mode==='edit'?'update':'store'}`),{forceFormData:true})};
const remove=()=>{if(!window.confirm(`Delete “${form.name}”? This cannot be undone.`))return; deleting.value=true; router.delete(route(`${isSubcategory.value?'sub-categories':'main-categories'}.delete`,{id:data.value.id}),{onFinish:()=>deleting.value=false})};
</script>
