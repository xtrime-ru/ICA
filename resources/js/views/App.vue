<template>
    <v-app id="app">
        <sidebar/>
        <notifications/>

        <v-main>
            <router-view></router-view>
        </v-main>

    </v-app>
</template>

<script>
    import Notifications from "resources/js/components/Notifications"
    import Sidebar from "resources/js/components/Sidebar"

    export default {
        components: {
            Notifications,
            Sidebar
        },
    }
</script>
