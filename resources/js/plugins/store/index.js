import Vue from "vue"
import Vuex from "vuex"
import user from "js/plugins/store/user"
import notifications from "js/plugins/store/notifications";

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        user,
        notifications,
    }
})
