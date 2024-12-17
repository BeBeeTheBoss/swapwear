<template>
    <Layout>
        <v-breadcrumbs class="my-5" :items="headers"></v-breadcrumbs>
        <div class="container">
            <div class="row flex justify-center items-center" style="height:60vh">
                <div class="col-lg-6 col-md-8 col-sm-12 col-12 shadow bg-blur rounded-lg p-3">
                    <div class="text-center text-h5">
                        Update main category
                    </div>
                    <div class="form mt-8">
                        <v-text-field label="Name" v-model="form.name" variant="outlined"></v-text-field>
                        <v-file-input prepend-icon="mdi-image-outline" clearable label="Choose icon"
                            @change="onFileChange" @input="form.icon = $event.target.files[0]" @click:clear="clearImage"
                            variant="outlined"></v-file-input>
                        <div class="flex justify-center">
                            <div v-if="previewImageUrl" style="position:relative">
                                <v-img class="rounded-lg mb-4" :width="100" :height="100" cover
                                    :src="previewImageUrl" />
                                <div style="position:absolute;top:-10px;right:-20px">
                                    <button @click="clearImage">
                                        <font-awesome-icon icon="fa-solid fa-circle-xmark" class="text-white fs-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <Button text="Update" class="py-3 mt-3 mb-2" @click="submit" :loading="loading"  style="width:49%"/>
                        <Button text="Delete" class="py-3 mt-3 mb-2" @click="deleteCategory" :loading="loading2"  style="width:49%;background-color:#C23238"/>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script setup>
import Layout from '../../Layouts/Layout.vue';
import Button from '../../Components/Button.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const headers = ['Resources', 'Main Categories', 'Edit'];

const props = defineProps({
    main_category: Object
})

const form = useForm({
    id : props.main_category.data.id,
    name: props.main_category.data.name,
    icon: props.main_category.data.icon,
})

const toast = useToast();
const previewImageUrl = ref(props.main_category.data.icon);
const loading = ref(false);
const loading2 = ref(false);

const clearImage = () => {
    previewImageUrl.value = null;
    form.icon = null;
}

const onFileChange = (event) => {
    const file = event.target.files[0];
    previewImageUrl.value = URL.createObjectURL(file);
}

const submit = () => {
    loading.value = true;

    if (form.name == '' || form.icon == '' || form.icon == null) {
        toast.info('Please input all fields');
        loading.value = false;
        return;
    }

    form.post(route('main-categories.update'), {
        onSuccess: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        }
    });
}

const deleteCategory = () => {
    router.delete(route('main-categories.delete', {id : props.main_category.data.id}), {
        onSuccess: () => {
            loading2.value = false;
        },
        onError: () => {
            loading2.value = false;
        }
    })
}

</script>

<style scoped>
.bg-blur {
    background-color: rgba(255, 255, 255, 0.14);
    backdrop-filter: blur(10px);
}

.input {
    width: 100%;
    height: 40px;
    border-radius: 6px;
    padding-left: 10px;
    border: rgba(255, 255, 255, 0.429) solid 1px;
    outline: none;
    background-color: transparent;
}
</style>
