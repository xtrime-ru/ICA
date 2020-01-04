import Vue from "vue"
import Vuex from "vuex"
import userModule from "js/plugins/store/user"

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        user: userModule
    }
})
