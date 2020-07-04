import _ from "lodash";
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

let state = _.clone(guest)

const mutations = {
    set(state: User, input: User): void {
        if (input.version && input.version !== guest.version) {
            input = _.clone(guest)
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
    apiToken: (state: User) => state.api_token,
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
    async init({commit, dispatch, getters}) {
        await commit('set', JSON.parse(localStorage.getItem(localStorageKey)) || guest)
        if (getters.apiToken) {
            this._vm.$http.post("auth/check").then(
                (response: UserResponse) => {},
                (error: UserResponse) => {
                    if (error.status === 401) {
                        commit('set', guest)
                        dispatch('notifications/add', {
                            text: 'Ошибка авторизации. Нужно войти заново.',
                            timeout: 5000,
                            color:'error'
                        }, {root:true})
                    }
                }
            )
        }
    },
    register({commit, state}, data) {
        return new Promise(async (resolve, reject) => {
            this._vm.$http.post("auth/register", JSON.parse(JSON.stringify(data))).then(
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
            this._vm.$http.post("auth/login", JSON.parse(JSON.stringify(data))).then(
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
                await this._vm.$http.post("auth/logout")
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

