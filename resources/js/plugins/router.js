import Vue from 'vue'
import VueRouter from "vue-router"
import store from "js/plugins/store/"
import roles from "js/plugins/store/roles"

Vue.use(VueRouter);

let routes = [
    {
        name: 'index',
        path: '/',
        component: () => import('js/views/Index'),
        props: true
    },
    {
        name: 'login',
        path: '/login',
        component: () => import('js/views/Login'),
        props: true,
        meta: {
            access: roles.guest
        }
    },
    {
        name: 'logout',
        path: '/logout',
        component: () => import('js/views/Logout'),
    },
    {
        name: 'register',
        path: '/register',
        component: () => import('js/views/Register'),
        props: true,
        meta: {
            access: roles.guest
        }
    },
    {
        name: 'settings',
        path: '/settings',
        component: () => import('js/views/Settings'),
        props: true,
        meta: {
            access: roles.user
        }
    },
    {
        name: '403',
        path: '/403',
        component: () => import('js/views/Error403'),
    },
    {
        name: '404',
        path: '/404',
        component: () => import('js/views/Error404'),
    },
    {
        name: 'catch-all',
        path: '*',
        component: () => import('js/views/Error404'),
    },
]
let router = new VueRouter({
    mode: 'history',
    routes,
})

router.beforeEach((to, from, next) => {
    const access = to.meta.access || roles.any
    const user = {
        hasAccess: store.getters['user/hasAccess'],
    }

    if (user.hasAccess(access)) {
        next()
    } else {
        switch (true) {
            case access !== roles.guest:
                next({
                    path: '/login',
                    query: {redirect: to.fullPath}
                })
                break;
            case access === roles.guest:
                next({
                    path: '/',
                })
                break;
            default:
                next({
                    path: '/403',
                })
                break;
        }
    }

})

export default router
