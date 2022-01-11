import {Source} from "~/plugins/store/sources"
import {app} from "~/app"
import {HttpResponse} from "vue-resource/types/vue_resource"

interface State {
    posts: Posts,
    postIds: Map<number,number>,
    lastId: number,
    hasMorePosts: boolean,
    loading: boolean
}

interface Post {
    id: number,
    url: string,
    image?: string,
    title: string,
    description: string,

    meta: Meta
    source: Source,
    created_at: string,
}

interface Meta {
    post_id: number,
    user_id: number,
    views: number,
    viewed: boolean,

    likes: number,
    liked: boolean,

    bookmarks: number,
    bookmarked: boolean
}

interface Posts extends Array<Post> {
}

interface PostsResponse extends HttpResponse {
    data: { posts: Posts, last_id:number, has_more_posts:boolean, errors: Array<string> },
}

interface MetaResponse extends HttpResponse {
    data: { posts_meta: Array<Meta>, errors: Array<string> },
}

let state: State = {
    posts: [],
    postIds: new Map(),
    lastId: 0,
    hasMorePosts: true,
    loading: true
};

const mutations = {
    setPosts(state: State, payload: Posts): void {
        state.posts = payload
        state.postIds = new Map()
        state.posts.forEach((post, key) => {
            state.postIds.set(post.id, key)
        })
    },
    setLastId(state:State, lastId:number): void {
        state.lastId = lastId
    },
    setHasMorePosts(state:State, hasMorePosts:boolean): void {
        state.hasMorePosts = hasMorePosts
    },
    updateMeta(state: State, meta:Meta) {
        let postkey = state.postIds.get(meta.post_id)
        state.posts[postkey].meta = {...state.posts[postkey].meta, ...meta}
    },
    setLoading(state: State, loading:boolean) {
        state.loading = loading
    },
}

const getters = {
    all: (state): Posts => state.posts,
    get: (state) => (id): Post => state.posts[state.postIds.get(parseInt(id))]
}

const actions = {

    load({commit, dispatch}, reload:boolean = false) {
        if (reload) {
            commit('setLastId', 0)
            commit('setHasMorePosts', true)
        }
        if (!state.hasMorePosts) {
            dispatch('notifications/add', {
                text: 'Больше нет постов',
                timeout: 5000,
                color: 'info'
            }, {root: true})
            return
        }

        commit('setLoading', true)
        return app.$http.post("posts/get", {'id': state.lastId}).then(
            (response: PostsResponse) => {
                commit('setPosts', response.data.posts)
                commit('setLastId', response.data.last_id)
                commit('setHasMorePosts', response.data.has_more_posts)

            },
            (error: PostsResponse) => {
                commit('setHasMorePosts', 0)
                dispatch('notifications/add', {
                    text: 'Ошибка при загрузке постов: ' + error.data.errors.join('; '),
                    timeout: 5000,
                    color: 'error'
                }, {root: true})
            }
        ).then(() => {
            commit('setLoading', false)
        });
    },
    updateMeta({commit, dispatch, rootGetters}, data:{postId:number, property:"viewed"|"liked"|"bookmarked", value:boolean}) {
        if (!rootGetters["user/hasAccess"]('user')) {
            if (data.property !== "viewed") {
                dispatch('notifications/add', {
                    text: 'Необходимо авторизоваться',
                    timeout: 5000,
                    color: 'info'
                }, {root: true})
            }
            return
        }
        let meta = {
            "post_id": data.postId
        };
        meta[data.property] = data.value;

        commit('updateMeta', meta)

        this._vm.$http.post("posts/updateMeta", {'posts': [meta]}).then(
            (response: MetaResponse) => {
                response.data.posts_meta.forEach((meta) => {
                    commit('updateMeta', meta)
                });
            },
            (error: MetaResponse) => {
                console.log(error.data.errors);
                dispatch('notifications/add', {
                    text: 'Ошибка при сохранении данных: ' + error.data.errors.join('; '),
                    timeout: 0,
                    color: 'error'
                }, {root: true})
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

