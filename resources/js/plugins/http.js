import Vue from "vue"
import VueResource from "vue-resource"
import store from "~/plugins/store"

Vue.use(VueResource)
Vue.http.options.root = '/api'
Vue.http.responseType = 'json'
Vue.http.timeout = 5000
Vue.http.credentials = true

const VueCookie = require('vue-cookie');
// Tell Vue to use the plugin
Vue.use(VueCookie);


Vue.http.interceptors.push(function(request) {
    // modify headers
    request.headers.set('X-XSRF-TOKEN', Vue.cookie.get('XSRF-TOKEN'));

    return function(response) {
        if (response.status === 401 || response.status === 419) {
            store.dispatch('user/logout')
        }
    };
});