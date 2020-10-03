interface State {
    posts: Posts,
    postIds: Map<number,number>,
    lastId: number,
    hasMorePosts: boolean,
    loading: boolean
}

interface Source {
    id: number,
    category_id: number,
    social_id: number,
    url: string,
    icon?: string,
    name: string,
    age_limit: bigint,
    likes: bigint,
    subscribers: bigint,
    views: bigint,
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

interface PostsResponse extends Omit<Response, 'body'> {
    body: { posts: Posts, last_id:number, has_more_posts:boolean, errors: Array<string> },
}

interface MetaResponse extends Omit<Response, 'body'> {
    body: { posts_meta: Array<Meta>, errors: Array<string> },
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
        state.posts = payload;
        state.postIds = new Map();
        state.posts.forEach((post, key)=>{
            state.postIds.set(post.id, key);
        })
        state.loading = false;
    },
    setLastId(state:State, lastId:number): void {
        state.lastId = lastId;
    },
    setHasMorePosts(state:State, hasMorePosts:boolean): void {
        state.hasMorePosts = hasMorePosts;
    },
    updateMeta(state: State, meta:Meta) {
        let postkey = state.postIds.get(meta.post_id)
        state.posts[postkey].meta = meta
    },
    setLoading(state: State, loading:boolean) {
        state.loading = loading;
    },
}

const getters = {
    all: (state): Posts => state.posts,
}

const actions = {
    async load({commit, dispatch}, reload:boolean = false) {
        if (reload) {
            commit('setLastId', 0);
            commit('setHasMorePosts', true);
        }
        if (!state.hasMorePosts) {
            dispatch('notifications/add', {
                text: 'Больше нет постов',
                timeout: 5000,
                color: 'info'
            }, {root: true})
            return;
        }

        commit('setLoading', true)
        return this._vm.$http.post("posts/get", {'id': state.lastId}).then(
            (response: PostsResponse) => {
                commit('setPosts', response.body.posts)
                commit('setLastId', response.body.last_id)
                commit('setHasMorePosts', response.body.has_more_posts)
            },
            (error: PostsResponse) => {
                console.log(error.body.errors);
                dispatch('notifications/add', {
                    text: 'Ошибка при загрузке постов: ' + error.body.errors.join('; '),
                    timeout: 5000,
                    color: 'error'
                }, {root: true})
            }
        )
    },
    async updateMeta({commit, dispatch}, meta:Meta) {
        commit('updateMeta', meta)

        this._vm.$http.post("posts/updateMeta", {'posts': [meta]}).then(
            (response: MetaResponse) => {
                response.body.posts_meta.forEach((meta) => {
                    commit('updateMeta', meta)
                });
            },
            (error: MetaResponse) => {
                console.log(error.body.errors);
                dispatch('notifications/add', {
                    text: 'Ошибка при сохранении данных: ' + error.body.errors.join('; '),
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

