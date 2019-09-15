<template>
    <v-app id="app">
        <v-navigation-drawer
            v-model="drawer"
            app
            style="overflow: visible;"
            width="200px;"
        >
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


            <v-list
                flat
            >
                <v-list-item
                    v-for="item in items"
                    :key="item.title"
                    :to="item.path"
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


            <template v-slot:append>
                <v-footer color="transparent" elevation="0">
                    <span class="secondary--text text--lighten-3">&copy; {{new Date().getFullYear()}}</span>
                </v-footer>
            </template>
            <template v-slot:prepend>
                <v-btn
                    fab
                    color="primary"
                    top
                    right
                    absolute
                    style="top:0; right: -6em; margin: 12px;"
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
    import Vuetify from 'vuetify'

    export default {
        data: () => ({
            drawer: null,
            items: [
                { title: 'Лента', icon: 'mdi-view-headline', path: '/' },
                { title: 'Вход', icon: 'mdi-login', path: '/login'  },
                { title: 'Регистрация', icon: 'mdi-account', path: '/register' },
            ],
        }),
    }
</script>
