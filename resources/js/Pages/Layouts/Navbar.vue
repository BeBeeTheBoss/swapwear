<template>
    <!-- <div :style="{ backgroundColor: $themeColor }" class="button-container col-lg-2 col-md-6 col-sm-11 col-11"
        style="position:fixed;bottom:10px;left:50%;transform: translate(-50%)">
        <Link :href="$route('dashboard')">
        <button class="button" :class="route().current('dashboard') ? 'click' : ''" style="position: relative">
            <font-awesome-icon icon="fa-solid fa-chart-simple" />
        </button>
        </Link>
        <Link :href="$route('resources')">
        <button class="button" :class="route().current('resources') || route().current('main-categories.*') ? 'click' : ''" style="position: relative">
            <font-awesome-icon icon="fa-solid fa-database" />
        </button>
        </Link>
        <button class="button">
            <font-awesome-icon icon="fa-solid fa-user-group" />
        </button>
        <button class="button">
            <font-awesome-icon icon="fa-solid fa-comments" />
        </button>
        <button class="button">
            <font-awesome-icon icon="fa-solid fa-gear" />
        </button>
    </div> -->
    <section :style="{ backgroundColor: $themeColor }" class="button-container col-lg-2 col-md-6 col-sm-11 col-11"
        style="position:fixed;bottom:10px;left:50%;transform: translate(-50%)">
        <Link :href="route('dashboard')">
        <label title="chart" for="chart" class="label">
            <input id="chart" name="page" type="radio" :checked="route().current('dashboard')" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path
                    d="M160 80c0-26.5 21.5-48 48-48l32 0c26.5 0 48 21.5 48 48l0 352c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-352zM0 272c0-26.5 21.5-48 48-48l32 0c26.5 0 48 21.5 48 48l0 160c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48L0 272zM368 96l32 0c26.5 0 48 21.5 48 48l0 288c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-288c0-26.5 21.5-48 48-48z" />
            </svg>
        </label>
        </Link>
        <Link :href="route('resources')">
        <label title="resources" for="resources" class="label">
            <input id="resources" name="page" type="radio"
                :checked="route().current('resources') || route().current('main-categories.*') || route().current('sub-categories.*')" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                <path
                    d="M448 80l0 48c0 44.2-100.3 80-224 80S0 172.2 0 128L0 80C0 35.8 100.3 0 224 0S448 35.8 448 80zM393.2 214.7c20.8-7.4 39.9-16.9 54.8-28.6L448 288c0 44.2-100.3 80-224 80S0 332.2 0 288L0 186.1c14.9 11.8 34 21.2 54.8 28.6C99.7 230.7 159.5 240 224 240s124.3-9.3 169.2-25.3zM0 346.1c14.9 11.8 34 21.2 54.8 28.6C99.7 390.7 159.5 400 224 400s124.3-9.3 169.2-25.3c20.8-7.4 39.9-16.9 54.8-28.6l0 85.9c0 44.2-100.3 80-224 80S0 476.2 0 432l0-85.9z" />
            </svg>
        </label>
        </Link>
        <label title="users" for="users" class="label">
            <input id="users" name="page" type="radio" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                <path
                    d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM609.3 512l-137.8 0c5.4-9.4 8.6-20.3 8.6-32l0-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2l61.4 0C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z" />
            </svg>

        </label>
        <label title="feedbacks" for="feedbacks" class="label">
            <input id="feedbacks" name="page" type="radio" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M512 240c0 114.9-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6C73.6 471.1 44.7 480 16 480c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c0 0 0 0 0 0s0 0 0 0s0 0 0 0c0 0 0 0 0 0l.3-.3c.3-.3 .7-.7 1.3-1.4c1.1-1.2 2.8-3.1 4.9-5.7c4.1-5 9.6-12.4 15.2-21.6c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208z" />
            </svg>
        </label>
        <label title="settings" for="settings" class="label">
            <input id="settings" name="page" type="radio" />
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z" />
            </svg>
        </label>
    </section>


</template>

<script setup>
import { route } from 'ziggy-js';


</script>

<style scoped>
/* .button-container {
    display: flex;
    align-items: center;
    height: 50px;
    justify-content: space-around;
    border-radius: 10px;
}

.button {
    outline: 0 !important;
    border: 0 !important;
    width: 40px;
    height: 40px;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: all ease-in-out 0.3s;
    cursor: pointer;
}

.click {
    color: rgb(71, 71, 56)
}

.icon {
    font-size: 20px;
} */

/* From Uiverse.io by 3bdel3ziz-T */
section {
    --col-orange: #d17842;
    --col-dark: #0c0f14;
    --col-darkGray: #ffffff;
    --col-gray: #aeaeae;

    /* width: fit-content; */
    padding: 8px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: space-evenly;
    background-color: var(--col-dark);
    border-radius: 30px;
}

.label {
    padding: 8px 18px;
    transition: all 200ms;
    display: inline-block;
}

.label input[type="radio"] {
    display: none;
}

.label>svg {
    transition: all 200ms;
    fill: var(--col-gray);
    width: 14px;
}

.label:hover:not(:has(input:checked))>svg {
    fill: #d17842;
    opacity: 0.6;
}

.label::before {
    content: "";
    display: block;
    width: 0%;
    height: 2px;
    border-radius: 5px;
    position: relative;
    left: 50%;
    top: 20px;
    background: var(--col-orange);
    transition: all 200ms;
}

.label>svg {
    transition: 300ms;
    fill: var(--col-darkGray);
    margin-top: 0;
}

.label:has(input:checked)>svg {
    fill: var(--col-orange);
    scale: 1.2;
    margin-top: -5px;
}

.label:has(input:checked)::before {
    width: 100%;
    left: 0;
    top: 25px;
}
</style>
