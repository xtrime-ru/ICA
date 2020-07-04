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
     * @param {Notification} notification
     * @returns {*}
     */
    add(state, notification) {
        notification.id = state.nextId
        state.nextId++
        state.notifications.unshift(notification)
    },
    /**
     *
     * @param state
     * @param {number} id
     */
    remove(state, id) {
        state.notifications.forEach((notification, index) => {
            if (notification.id === id) {
                state.notifications.splice(index, 1)
                return true
            }
        })
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
        let id = state.nextId;
        console.log(notification);
        commit('add', notification)

        if (typeof notification.timeout !== "number") {
            notification.timeout = defaultTimeout
        }

        if (notification.timeout) {
            setTimeout(() => commit('remove', id), notification.timeout)
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
