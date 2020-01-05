import Vue from "vue"
import Vuex from "vuex"
import userModule from "js/plugins/store/user"
import notificationsModule from "js/plugins/store/notifications";

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        user: userModule,
        notifications: notificationsModule,
    }
})
