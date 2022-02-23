<template>
    <v-navigation-drawer
            v-model="drawer"
            app
            width=230
            :mini-variant="!sideBarHover && !this.$vuetify.breakpoint.mobile"
    >
        <v-hover
                :close-delay="300"
                v-model="sideBarHover"
        >
            <div>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title class="title primary--text">
                            ICA
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            internet content aggregator
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-list>
                    <v-list-item
                            v-for="item in items"
                            :key="item.title"
                            :to="item.path"
                            v-show="hasAccess(item.access)"
                            active-class="primary--text"
                    >
                        <v-list-item-icon>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>{{ item.title }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list>
            </div>
        </v-hover>

        <template v-slot:append>
            <v-footer color="transparent" elevation="0">
                <span class="copyright secondary--text text--lighten-3">&copy;&nbsp;{{new Date().getFullYear()}}</span>
            </v-footer>
        </template>
        <template v-slot:prepend>
            <v-btn
                    class="sidebar-controll-button"
                    fab
                    color="primary"
                    top
                    right
                    absolute
                    rounded
                    @click.stop="drawer = !drawer"
            >
                <v-icon color="black" v-show="drawer" transition="fade-out-in">mdi-close</v-icon>
                <v-icon color="black" v-show="!drawer" transition="fade-out-in">mdi-menu</v-icon>
            </v-btn>
        </template>

    </v-navigation-drawer>
</template>

<script>
    import {mapGetters} from "vuex"
    import roles from "resources/js/plugins/store/roles"

    export default {
        name: "Sidebar",
        data: () => ({
            drawer: null,
            sideBarHover: null,
            items: [
                {title: "Лента", icon: "mdi-view-headline", path: "/", access: roles.any},
                {title: "Источники", icon: "mdi-apps", path: "/sources", access: roles.user},
                {title: "Вход", icon: "mdi-login", path: "/login", access: roles.guest},
                {title: "Регистрация", icon: "mdi-account", path: "/register", access: roles.guest},
                {title: "Настройки", icon: "mdi-cog-outline", path: "/settings", access: roles.user},
            ]
        }),
        created() {
            this.drawer = !this.$vuetify.breakpoint.mobile;
        },
        computed: mapGetters({
            hasAccess: "user/hasAccess",
        }),
    }
</script>

<style scoped>
    .v-navigation-drawer {
        overflow: visible;
    }

    .v-navigation-drawer .sidebar-controll-button {
        top: 4px!important;
        right: -6em!important;
        visibility: visible;
        margin: 12px;
    }

    .v-footer {
      align-items: center;
      justify-content: center;
    }
    .copyright {
      font-size: 0.7rem;
    }
</style>