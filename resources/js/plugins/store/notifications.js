const state = {
    success: [],
    info: [],
    error: [],
}

const getters = {}

const mutations = {
    add(state, type, text) {
        return state[type].push(text)
    },
    remove(state, type, key) {
        state[type].splice(key, 1)
    }
}

const actions = {
    add({dispatch, commit, getters, rootGetters}, type, text, ttl = 5000) {
        let index = commit('add', type, text)
        if (ttl) {
            setTimeout(() => commit('remove', type, index), ttl)
        }
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
