<template>
    <transition-group name="list-fade" tag="div" class="notification-container"
                      :class="{'full-width':$vuetify.breakpoint.xsOnly}"
                      v-on:before-leave="beforeAnimation"
                      v-on:after-leave="afterAnimation"
                      ref="notifications"
    >
        <v-snackbar
            class="list-fade-item"
            v-for="( snackbar, index ) in getNotifications" v-bind:key="snackbar.id"
            :value="true"
            top right
            :color="snackbar.color"
            multi-line
            vertical
            :timeout="-1"
            dark
        >
            {{snackbar.text}}
            <v-btn
                dark
                text
                @click="removeNotification(index)"
            >
                <v-icon dark>mdi-close</v-icon>
                Close
            </v-btn>
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
                if (this.getNotifications.length > 1) {
                    $el.style["overflow-x"] = "visible";
                    $el.style["height"] = "100vh";
                } else {
                    $el.style["overflow"] = "visible";
                }

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

<style scoped>

    .notification-container {
        position: fixed;
        right: 0;
        z-index: 1000;
        padding: 16px;
        width: auto;
        max-height: 100vh;
        overflow: auto;
    }

    .notification-container .full-width {
        width: 100%;
    }

    .notification-container .v-snack {
        position: relative;
        margin: 0 auto 8px 0;
        z-index: inherit;
        top: 0;
        left: 0;
    }

    .list-fade-item {
        transition-property: transform, opacity;
        transition-duration: 0.5s;
    }

    .list-fade-enter,
    .list-fade-leave-to {
        opacity: 0;
        transform: translateX(50%);
    }

    .list-fade-leave-active {
        position: absolute !important;
        top: unset !important;
    }
</style>
