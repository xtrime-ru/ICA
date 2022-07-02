<template>
    <transition-group name="list-fade" tag="div" class="notification-container"
                      :class="{'full-width':$vuetify.breakpoint.xsOnly}"
                      v-on:before-leave="beforeAnimation"
                      v-on:after-leave="afterAnimation"
                      ref="notifications"
    >
        <v-snackbar
            class="list-fade-item"
            v-for="snackbar in getNotifications" v-bind:key="snackbar.id"
            :value="true"
            top right
            :color="snackbar.color"
            multi-line
            vertical
            :absolute=true
            :timeout=-1
            dark
        >
            {{snackbar.text}}
            <template v-slot:action="{ attrs }">
                <v-btn
                    dark
                    text
                    v-bind="attrs"
                    @click="removeNotification(snackbar.id)"
                >
                    <v-icon dark>mdi-close</v-icon>
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </transition-group>


</template>

<script>
    import {mapActions, mapGetters, mapMutations} from "vuex"

    export default {
        name: "Notifications",
        methods: {
            ...mapMutations({
                removeNotification: "notifications/remove",
            }),
            ...mapActions({
                addNotifications: "notifications/add",
            }),
            beforeAnimation() {
                let $el = this.$refs.notifications.$el;
                $el.style["overflow"] = "visible";
            },
            afterAnimation() {
                let $el = this.$refs.notifications.$el;
                $el.setAttribute("style", "");
            }
        },
        computed: {
            ...mapGetters({
                getNotifications: "notifications/get"
            }),
        },
    }
</script>

<style>

    .notification-container {
        position: fixed;
        right: 0;
        z-index: 1000;
        padding: 16px;
        max-height: 100vh;
        overflow: visible;
    }

    .notification-container.full-width {
        padding: 0;
        width: 100%;
    }

    .notification-container .v-snack {
        position: relative;
        margin: 0 0 auto auto;
        z-index: inherit;
        top: 0;
        right: 0;
        height: auto;
    }

    .notification-container .v-snack__wrapper {
      min-width: 300px;
      max-width: 400px;
    }

    .notification-container .list-fade-item {
        transition-property: all;
        transition-duration: 0.5s;
    }

    .notification-container .list-fade-enter,
    .notification-container .list-fade-leave-to {
        opacity: 0;
        transform: translateX(100%);
    }

    .notification-container .list-fade-leave-active {
        max-height: 0;
        margin-bottom: 0 !important;
    }
</style>
