<template>
    <div>
        <header class="header">
            <div class="header__container container">
                <div class="header__logo">
                    Easy Learn
                </div>
                <router-link class="link" :to="{name: 'words.index'}">Показать все словари</router-link>
            </div>
        </header>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "Index",
    data() {
        return {

        }
    }
}
</script>

<style scoped>

</style>
