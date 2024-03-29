import Vue from 'vue'
import VueRouter from "vue-router"
import store from "~/plugins/store/"
import roles from "~/plugins/store/roles"

Vue.use(VueRouter);

let routes = [
    {
        name: 'index',
        path: '/',
        component: () => import('~/views/Index'),
        props: true
    },
    {
        name: 'login',
        path: '/login',
        component: () => import('~/views/Auth/Login'),
        props: true,
        meta: {
            access: roles.guest
        }
    },
    {
        name: 'logout',
        path: '/logout',
        component: () => import('~/views/Auth/Logout'),
        props: route => ({ redirect: route.query.redirect || route.params.redirect })
    },
    {
        name: 'register',
        path: '/register',
        component: () => import('~/views/Auth/Register'),
        props: true,
        meta: {
            access: roles.guest
        }
    },
    {
        name: 'repass',
        path: '/password/reset/:token?',
        component: () => import('~/views/Auth/Repass'),
        props: route => ({ token: route.params.token, email: route.query.email }),
        meta: {
            access: roles.guest
        }
    },
    {
        name: 'settings',
        path: '/settings',
        component: () => import('~/views/Settings'),
        props: true,
        meta: {
            access: roles.user
        }
    },
    {
        name: 'sources',
        path: '/sources',
        component: () => import('~/views/Sources'),
        props: true,
        meta: {
            access: roles.user
        }
    },
    {
        name: 'source_add',
        path: '/source_add',
        component: () => import('~/views/SourceAdd'),
        props: true,
        meta: {
            access: roles.user
        }
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
let router = new VueRouter({
    mode: 'history',
    routes,
    scrollBehavior (to, from, savedPosition) {
        return { x: 0, y: 0 }
    }
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
