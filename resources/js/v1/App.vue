<template>
        <p>Installed Vue 3 in Laravel 9 with Vite </p>
        <label for="locale">Locale: </label>
        <select v-model="$i18n.locale" id="locale">
            <option v-for="locale in $i18n.availableLocales" :value="locale">{{ locale }}</option>
        </select>
        <h1 class="green">{{ $t(header) }}</h1>
        <RouterView />
</template>

<script>

export default {
    name: "App",

    data() {
        return {
            'title' : '',
            'header' : ''
        }
    },
    mounted() {
                this.title = "pages." + this.$router.currentRoute._value.name + ".title"
                this.header = "pages." + this.$router.currentRoute._value.name + ".header"
    },

    methods: {
        getCurRoute() {
            // Get array of links
            const routes = JSON.parse(localStorage.getItem('routes'));
            let rt = {}
            rt = routes.find(el => el.name == this.$route.name);
            if (typeof (rt) === 'object') {
                return rt;
            } else {
                return {icon: 'fas fa-home', title: 'Главная'}
            }
        }
    },
}
</script>
