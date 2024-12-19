<template>
    <layout>
        <v-breadcrumbs class="my-5" :items="headers"></v-breadcrumbs>
        <div class="container">
            <Link :href="route('resources')">
            <span class="h5 cursor-pointer">
                <font-awesome-icon icon="fa-solid fa-chevron-left" /> Back
            </span>
            </Link>
        </div>
        <div class="container mt-8 flex justify-between items-center">
            <div class="h5">
                Main Categories
            </div>
            <div>
                <Link :href="route('main-categories.create')">
                <Button text="Create" icon="fa-solid fa-plus" />
                </Link>
            </div>
        </div>
        <div class="container mb-20">
            <div class="row mt-6">
                <div class="col-lg-3 col-md-4 col-sm-6 col-6 cursor-pointer my-2"
                    v-for="main_category in props.main_categories.data" :key="main_category">
                    <Link :href="route('main-categories.edit', { id: main_category.id })">
                    <div class="border shadow-sm p-2 bg-blur rounded-lg">
                        <div class="flex items-center">
                            <div>
                                <img :src="main_category.icon"
                                    style="width:35px;height:35px;object-fit:cover;object-position:center">
                            </div>
                            <div class="ms-3" style="font-size:17px">
                                {{ main_category.name }}
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>
    </layout>
</template>

<script setup>
import Layout from '../../Layouts/Layout.vue';
import Button from '../../Components/Button.vue';
import { ref, onMounted, onUpdated } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();
const page = usePage();
const headers = ['Resources', 'Main Categories'];

const props = defineProps({
    main_categories: Array
})

const goBack = () => {
    window.history.back();
}

onMounted(() => {


    // if (page.props.flash.success) {
    //     toast.success(page.props.flash.success);
    //     page.props.flash.success = null;
    // } else if (page.props.flash.error) {
    //     toast.error(page.props.flash.error);
    //     page.props.flash.error = null;
    // }

})

onUpdated(() => {



    if (page.props.flash.success) {
        toast.success(page.props.flash.success);
        page.props.flash.success = null;
    } else if (page.props.flash.error) {
        toast.error(page.props.flash.error);
        page.props.flash.error = null;
    }

})

</script>

<style scoped>
.bg-blur {
    background-color: rgba(255, 255, 255, 0.236);
    backdrop-filter: blur(10px);
}
</style>
