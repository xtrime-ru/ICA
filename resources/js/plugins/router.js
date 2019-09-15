import Vue from 'vue'
import VueRouter from "vue-router"

Vue.use(VueRouter);

let routes = [
    {
        name: 'index',
        path: '/',
        component: () => import('~/views/Index'),
        props: true,
    },
    {
        name: 'login',
        path: '/login',
        component: () => import('~/views/Login'),
        props: true,
    },
    {
        name: 'register',
        path: '/register',
        component: () => import('~/views/Register'),
        props: true,
    },
    {
        name: '403',
        path: '/403',
        component: () => import('~/views/Error403'),
    },
    {
        name: '404',
        path: '/404',
        component: () => import('~/views/Error404'),
    },
    {
        name: 'catch-all',
        path: '*',
        component: () => import('~/views/Error404'),
    },
]

export default new VueRouter({
    mode: 'history',
    routes,
})
