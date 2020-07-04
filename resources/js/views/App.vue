<template>
    <v-app id="app">
        <sidebar/>
        <notifications/>

        <v-main>
            <v-container
                    class="fill-height"
            >
                <router-view></router-view>
            </v-container>
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
