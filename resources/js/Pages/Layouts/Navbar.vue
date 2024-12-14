<template>
    <div :style="{ backgroundColor: $themeColor }" class="button-container col-lg-2 col-md-6 col-sm-11 col-11"
        style="position:fixed;bottom:10px;left:50%;transform: translate(-50%)">
        <Link :href="$route('dashboard')">
        <button class="button" :class="route().current('dashboard') ? 'click' : ''" style="position: relative">
            <font-awesome-icon icon="fa-solid fa-chart-simple" />
            <!-- <div v-if="route().current('dashboard')" style="position:absolute;bottom:3px;width:15px;height:4px;background-color:blue;border-radius:20px"></div> -->
        </button>
        </Link>
        <Link :href="$route('resources')">
        <button class="button" :class="route().current('resources') || route().current('resources.*') ? 'click' : ''" style="position: relative">
            <font-awesome-icon icon="fa-solid fa-database" />
            <!-- <div v-if="route().current('resources') || route().current('resources.*')" style="position:absolute;bottom:3px;width:15px;height:4px;background-color:blue;border-radius:20px"></div> -->
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
    </div>

</template>

<script setup>

</script>

<style scoped>
.button-container {
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
    /* border-radius: 50%; */
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    transition: all ease-in-out 0.3s;
    cursor: pointer;
}

.click {
    transform:scale(1.3);
    color:black
}

.icon {
    font-size: 20px;
}
</style>
