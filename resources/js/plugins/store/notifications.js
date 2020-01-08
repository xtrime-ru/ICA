const defaultTimeout = 6000

const state = {
    /**
     * @typedef {{id:number, text: string, timeout: number, color: string }} Notification
     */
    notifications: [],
    nextId:0,

}

const getters = {
    get(state) {
        return state.notifications
    }
}

const mutations = {
    /**
     *
     * @param state
     * @param {Notification[]} notifications
     * @returns {*}
     */
    add(state, notifications) {
        if (!Array.isArray(notifications)) {
            notifications = [notifications];
        }
        notifications.forEach((notification) => {
            notification.id = state.nextId
            state.nextId++
            state.notifications.push(notification)
        })
    },
    /**
     *
     * @param state
     * @param {number} key
     */
    remove(state, key) {
        state.notifications.splice(key, 1)
        if (state.notifications.length === 0) {
            state.nextId = 0
        }

    },
    removeAll(state) {
        state.notifications = [];
        state.nextId = 0
    }
}

const actions = {
    /**
     *
     * @param dispatch
     * @param commit
     * @param getters
     * @param rootGetters
     * @param {Notification} notification
     */
    add({dispatch, commit, getters, rootGetters}, notification) {
        let index = commit('add', notification)
        if (typeof notification.timeout !== "number") {
            notification.timeout = defaultTimeout
        }
        if (notification.timeout) {
            setTimeout(() => commit('remove', index), notification.timeout)
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
