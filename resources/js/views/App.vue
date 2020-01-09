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
    import Notifications from "js/components/Notifications"
    import Sidebar from "js/components/Sidebar"

    export default {
        components: {
            Notifications,
            Sidebar
        },
    }
</script>
