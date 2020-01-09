import Vue from "vue"
import VueResource from "vue-resource"
import store from "~/plugins/store"

Vue.use(VueResource)
Vue.http.options.root = '/api'
Vue.http.responseType = 'json'
Vue.http.timeout = 5000

store.watch((state, getters) => getters['user/apiToken'], (apiToken) => {
    if (apiToken) {
        Vue.http.headers.common['Authorization'] = 'Bearer ' + apiToken
    } else {
        delete Vue.http.headers.common['Authorization']
    }
})