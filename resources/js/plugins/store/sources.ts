import {app} from "~/app"
import {HttpResponse} from "vue-resource/types/vue_resource"

export interface Source {
    id: number,
    category_id: number,
    social_id: number,
    url: string,
    icon?: string,
    name: string,
    age_limit: number,
    likes: number,
    subscribers: number,
    views: number,
}

interface Category {
    id: number,
    style: string,
    name: string,
    slug: string,
}

interface Sources extends Array<Source>{}
interface Categories extends Array<Category> {}

interface State {
    categories: Categories,
    sources: Map<number, Source>,
    sources_enabled: Map<number, boolean>
    loading: boolean,
}

interface SourcesResponse extends HttpResponse {
    body: {
        sources: Sources,
        categories: Categories,
        sources_enabled: Array<number>,
        errors: Array<string>
    },
}

interface ToggleResponse extends HttpResponse {
    body: {
        errors: Array<string>
    },
}

let state: State = {
    categories: [],
    sources: new Map(),
    sources_enabled: new Map(),
    loading: true,
};

const mutations = {
    setLoading(state: State, loading:boolean) {
        state.loading = loading
    },
    setSources(state: State, payload: Sources): void {
        payload.forEach((source, key) => {
            state.sources.set(source.id, source)
        })
    },
    setSourcesEnabled(state: State, payload: Array<number>): void {
        payload.forEach((id, key) => {
            state.sources_enabled.set(id, true)
        })
    },
    setCategories(state: State, payload: Categories): void {
        state.categories = payload
    },
    toggleSource(state: State, id: number, enabled: Boolean): void {
        if (enabled) {
            state.sources_enabled.set(id, true);
        } else {
            state.sources_enabled.delete(id);
        }
    }
}
const getters = {
    getByCategory: (state: State) => (categoryId: number) => {
        let result = [];
        state.sources.forEach((source) => {
            if (source.category_id === categoryId) {
                result.push(source)
            }
        })
        return result
    },
    isEnabled: (state: State) => (sourceId: number) => state.sources_enabled.get(sourceId),
}
const actions = {
    load({commit, dispatch}) {
        commit('setLoading', true)
        return app.$http.post("sources/get").then(
            (response: SourcesResponse): void => {
                commit('setSources', response.body.sources)
                commit('setCategories', response.body.categories)
                commit('setSourcesEnabled', response.body.sources_enabled)
            },
            (error: SourcesResponse): void => {
                dispatch('notifications/add', {
                    text: 'Ошибка при загрузке источников: ' + error.body.errors.join('; '),
                    timeout: 5000,
                    color: 'error'
                }, {root: true})
            }
        ).then(() => {
            console.log('loaded');
            commit('setLoading', false)
        })
    },
    toggleSource({commit, dispatch, rootGetters}, data:{sourceId:number, enabled:boolean}) {
        if (!rootGetters["user/hasAccess"]('user')) {
            dispatch('notifications/add', {
                text: 'Необходимо авторизоваться',
                timeout: 5000,
                color: 'info'
            }, {root: true})
            return
        }

        commit('toggleSource', data.sourceId, data.enabled)
        console.log(data)
        this._vm.$http.post("sources/toggle", {'source_id': data.sourceId, 'enabled': data.enabled}).then(
            (response: ToggleResponse) => {
            },
            (error: ToggleResponse) => {
                console.log(error.data.errors);
                dispatch('notifications/add', {
                    text: 'Ошибка при сохранении данных: ' + error.data.errors,
                    timeout: 0,
                    color: 'error'
                }, {root: true})

                commit('toggleSource', data.sourceId, !data.enabled)
            }
        )
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}