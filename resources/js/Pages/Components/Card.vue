<template>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12 p-3">
        <div class="card pb-4 flex flex-col justify-between" style="position:relative"
            :class="business.status == 'pending' ? 'opacity-55' : ''">

            <img v-if="business.status == 'approved'" src="../images/approved.png"
                style="width:30px;position:absolute;top:7px;right:7px">
            <img v-else src="../images/pending.png" style="width:23px;position:absolute;top:7px;right:7px">

            <div>
                <img class="rounded-lg shadow-sm m-3" v-if="business.logo" :src="business.logo"
                    style="width:70px;height:70px;object-fit:cover;object-position:center">
                <img class="rounded-lg shadow-sm m-3" v-else src="../images/no_image.png"
                    style="width:70px;height:70px;object-fit:cover;object-position:center">

                <div class="ms-3 fw-bold">
                    {{ business.name }}
                </div>

                <div class="text-muted ms-3 " style="font-size:13px">
                    {{ business.address }}
                </div>

                <div class="ms-3 mt-3">
                    <div>
                        <font-awesome-icon icon="fa-solid fa-phone" class="me-1" :style="{ color: $themeColor }" />
                        {{ business.phone }}
                    </div>
                </div>

            </div>
            <div class="ms-3">
                <Link :href="`/business/details?shop_id=${business.id}`">
                    <Button class="mt-3" :class="business.status == 'pending' ? 'disabled' : ''" text="Manage" />
                </Link>
            </div>
        </div>
    </div>

</template>

<script setup>
import Button from './Button.vue';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    business: Object
})

</script>

<style scoped>
.logo svg {
    height: 48px;
    width: 54px;
}

::selection {
    background-color: rgba(0, 0, 0, 0);
}

.card {
    background: radial-gradient(circle, white 0%, #d5d5d5 100%);
    width: 100%;
    height: 100%;
    border-radius: 20px;
    position: relative;
    box-shadow: 3px 3px 17px 0px rgba(0, 0, 0, 0.4);
}

.card .remove,
.card .logo {
    position: absolute;
    display: inline-block;
    cursor: pointer;
}

.card .remove {
    top: 30px;
    right: 30px;
    opacity: 0.9;
    color: black;
}

.card .remove:hover {
    color: #f06b5d;
}

.card .logo {
    top: 20px;
    left: 30px;
    font-size: 3em;
    opacity: 1;
}

.card .number,
.card .owner {
    display: block;
    position: absolute;
    left: 30px;
    cursor: default;
    color: black;
    opacity: 0.9;
    transition: color 0.7s ease-out;
}

.card .number {
    bottom: 60px;
    letter-spacing: 4px;
}

.card .owner {
    bottom: 30px;
    letter-spacing: 1px;
}

.wave-group {
    position: relative;
}

.wave-group .input {
    font-size: 16px;
    padding: 10px 10px 10px 5px;
    display: block;
    width: 200px;
    border: none;
    border-bottom: 1px solid #515151;
    background: transparent;
}

.wave-group .input:focus {
    outline: none;
}

.wave-group .label {
    color: #999;
    font-size: 18px;
    font-weight: normal;
    position: absolute;
    pointer-events: none;
    left: 5px;
    top: 10px;
    display: flex;
}

.wave-group .label-char {
    transition: 0.2s ease all;
    transition-delay: calc(var(--index) * 0.05s);
}

.wave-group .input:focus~label .label-char,
.wave-group .input:valid~label .label-char {
    transform: translateY(-20px);
    font-size: 14px;
    color: #6e7fc1;
}

.wave-group .bar {
    position: relative;
    display: block;
    width: 200px;
}

.wave-group .bar:before,
.wave-group .bar:after {
    content: "";
    height: 2px;
    width: 0;
    bottom: 1px;
    position: absolute;
    background: #0727a6;
    transition: 0.2s ease all;
    -moz-transition: 0.2s ease all;
    -webkit-transition: 0.2s ease all;
}

.wave-group .bar:before {
    left: 50%;
}

.wave-group .bar:after {
    right: 50%;
}

.wave-group .input:focus~.bar:before,
.wave-group .input:focus~.bar:after {
    width: 50%;
}
</style>
