<template>
    <v-app id="app">
        <v-navigation-drawer
            v-model="drawer"
            app
            :mini-variant="miniSidebar"
            :mobile-break-point="1024"
        >
            <div v-on:mouseenter="miniSidebar=false" v-on:mouseleave="miniSidebar=true">
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


            <template v-slot:append>
                <v-footer color="transparent" elevation="0">
                    <span class="secondary--text text--lighten-3">&copy; {{new Date().getFullYear()}}</span>
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
                    @click.stop="drawer = !drawer"
                >
                    <v-icon color="black" v-show="drawer" transition="fade-out-in">mdi-close</v-icon>
                    <v-icon color="black" v-show="!drawer" transition="fade-out-in">mdi-menu</v-icon>
                </v-btn>
            </template>

        </v-navigation-drawer>

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
    import { mapGetters } from 'vuex'
    import roles from 'js/plugins/store/roles'

    export default {
        data: () => ({
            drawer: null,
            miniSidebar: true,
            items: [
                { title: 'Лента', icon: 'mdi-view-headline', path: '/', access: roles.any},
                { title: 'Вход', icon: 'mdi-login', path: '/login', access: roles.guest},
                { title: 'Регистрация', icon: 'mdi-account', path: '/register', access: roles.guest},
                { title: 'Настройки', icon: 'mdi-settings', path: '/settings', access: roles.user},
                { title: 'Выход', icon: 'mdi-logout', path: '/logout', access: roles.user},
            ]
        }),
        computed: mapGetters({
            hasAccess: 'user/hasAccess'
        })
    }
</script>
