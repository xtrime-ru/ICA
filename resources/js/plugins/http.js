import Vue from "vue"
import VueResource from "vue-resource"

Vue.use(VueResource)
Vue.http.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]')
