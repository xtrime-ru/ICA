import roles from "~/plugins/store/roles"

const localStorageKey: string = 'user'

interface User {
    version?: string,
    role: string
    api_token: string | null,
}

interface UserResponse extends Omit<Response, 'body'> {
    body: {user:User, errors:object},
}

const guest: User = {
    version: '1.0.0',
    role: roles.guest,
    api_token: null
}

let state = Object.assign({}, guest)

const mutations = {
    set(state: User, input: User): void {
        if (input.version && input.version !== guest.version) {
            input = Object.assign({}, guest)
        }

        state.version = input.version || guest.version
        state.api_token = input.api_token
        state.role = input.role

        if (state.api_token) {
            localStorage.setItem(localStorageKey, JSON.stringify(state))
        } else {
            localStorage.removeItem(localStorageKey)
        }
    },
}

const getters = {
    hasAccess: (state: User) => (role?: string): boolean => {
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
    async init({commit, dispatch, state}) {
        commit('set', JSON.parse(localStorage.getItem(localStorageKey)) || guest)
        await this._vm.$http.post("auth/me").then(
            (response: UserResponse) => {
                commit('set', response.body.user)
            },
            (error: UserResponse) => {
                if (error.status === 401) {
                    if (state.role !== 'guest') {
                        dispatch('notifications/add', {
                            text: 'Ошибка авторизации. Нужно войти заново.',
                            timeout: 5000,
                            color:'error'
                        }, {root:true})
                    }
                    commit('set', guest)
                }
            }
        )
    },
    register({commit, state}, data) {
        return new Promise(async (resolve, reject) => {
            this._vm.$http.post("/register", JSON.parse(JSON.stringify(data))).then(
                (response: UserResponse) => {
                    commit('set', response.body.user)
                    resolve(state)
                },
                (error: UserResponse) => {
                    reject(error.body.errors)
                }
            )
        })
    },
    login({commit, state}, data) {
        return new Promise(async (resolve, reject) => {
            await this._vm.$http.get("/sanctum/csrf-cookie")
            this._vm.$http.post("/login", JSON.parse(JSON.stringify(data))).then(
                (response: UserResponse) => {
                    commit('set', response.body.user)
                    resolve(state)
                },
                (error: UserResponse) => {
                    reject(error.body.errors)
                }
            )
        })
    },
    async logout({commit}) {
            try {
                await this._vm.$http.post("/logout")
            } catch (e) {
                console.log(e)
            }

            commit('set', guest)
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}

