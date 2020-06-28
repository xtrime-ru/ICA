import Vue from "vue"
import Vuex from "vuex"
import user from "~/plugins/store/user"
import notifications from "~/plugins/store/notifications";
import posts from "~/plugins/store/posts"

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        user,
        notifications,
        posts
    }
})
