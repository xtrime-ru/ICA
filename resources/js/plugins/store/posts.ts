interface Post {
    id: bigint,
    url: string,
    image?: string,
    title: string,
    description: string,
    views: bigint,
    likes: bigint,
    bookmarks: bigint,
}

interface Posts extends Array<Post>{}

interface PostsResponse extends Omit<Response, 'body'> {
    body: {posts:Posts, errors:object},
}

let state = {
    posts: [],
    lastId: 0,
    hasMorePosts: true,
};

const mutations = {
    set(state, input: Posts): void {
       state.posts = input;
       if (input.length) {
           state.lastId = input[input.length - 1].id;
       } else {
           state.lastId = 0;
           state.hasMorePosts = false;
       }
    },
}

const getters = {
    all: (state) => state.posts,
}

const actions = {
    async load({commit}) {
        if (!state.hasMorePosts) {
            commit('notifications/add', {
                text: 'Больше нет постов',
                timeout: 5,
                color:'info'
            }, {root:true})
            return;
        }

        this._vm.$http.post("posts/get", {'id': state.lastId}).then(
            (response: PostsResponse) => {
                commit('set', response.body.posts)

            },
            (error: PostsResponse) => {
                console.log(error.body.errors);
                commit('notifications/add', {
                    text: 'Ошибка при загрузке постов',
                    timeout: 5,
                    color:'error'
                }, {root:true})
            }
        )
    },

}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}

