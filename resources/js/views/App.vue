<template>
    <v-app id="app">
        <sidebar/>
        <notifications/>

        <v-content>
            <v-container
                    class="fill-height"
                    fluid
            >
                <router-view></router-view>
            </v-container>
        </v-content>

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
