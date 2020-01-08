import roles from "js/plugins/store/roles"
const localStorageKey = 'user'

/**
 * @typedef {{role: string, api_token: null|string, name: null|string, version: string, email: null|string}} User
 * @type User
 */
const guest = {
    version: '1.0.0',
    role: roles.guest,
    api_token: null,
    email: null,
    name: null,
}

let state = JSON.parse(localStorage.getItem(localStorageKey)) || guest
if (state.version !== guest.version) {
    localStorage.removeItem(localStorageKey)
    state = _.clone(guest)
}

const mutations = {
    /**
     *
     * @param state
     * @param {User} input
     */
    set(state, input) {
        if (input) {
            state.version = guest.version
            state.api_token = input.api_token || null
            state.role = input.role || roles.guest
            state.email = input.email || null
            state.name = input.name || null
        }

        if (state.api_token) {
            localStorage.setItem(localStorageKey, JSON.stringify(state))
        } else {
            localStorage.removeItem(localStorageKey)
        }
    },
}

const getters = {
    get(state) {
        return state
    },
    hasAccess: (state) => (role) => {
        if (!role) {
            role = roles.any
        }
        switch (role) {
            case roles.any:
                return true
            case roles.guest:
            case roles.admin:
                return state.role === role
            case roles.user:
                return [roles.user, roles.admin].indexOf(state.role) !== -1
            default:
                return false
        }
    },
}

const actions = {
    register({dispatch, commit, getters, rootGetters}, data) {
        return new Promise((resolve, reject) => {
            this._vm.$http.post("auth/register", JSON.parse(JSON.stringify(data))).then(
                response => {
                    commit('set', response.body.user)
                    resolve(getters.state)
                },
                error => {
                    reject(error.body.errors)
                }
            )
        })
    },
    login({dispatch, commit, getters, rootGetters}, data) {
        return new Promise((resolve, reject) => {
            this._vm.$http.post("auth/login", JSON.parse(JSON.stringify(data))).then(
                response => {
                    commit('set', response.body.user)
                    resolve(getters.state)
                },
                error => {
                    reject(error.body.errors)
                }
            )
        })
    },
    logout({dispatch, commit, getters, rootGetters}) {
        return new Promise((resolve, reject) => {
            this._vm.$http.post("auth/logout").then(
                response => {
                    commit('set', guest)
                    resolve(getters.state)
                },
                error => {
                    reject(error.body.errors)
                }
            )
        })
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}

