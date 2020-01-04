import Vue from "vue"
import VueResource from "vue-resource"

Vue.use(VueResource)
Vue.http.options.root = '/api'
Vue.http.responseType = 'json'
Vue.http.timeout = 5000
