<template>
    <div>
    <h1 class="float-right">Index page</h1>
    <ul>
        <li
                v-for="post in posts"
                :key="post.id">
            {{ post.title }} - {{ post.views}}
            <br>
        </li>
    </ul>
        <button
                :disabled="!hasMorePosts"
                @click="get">
            Load more
        </button>
    </div>
</template>

<script>
    import { mapState, mapActions } from 'vuex'

    export default {
        computed: mapState({
            posts: state => state.posts.posts,
            hasMorePosts: state => state.posts.hasMorePosts
        }),
        methods: mapActions('posts', [
            'load'
        ]),
        async created() {
            if (!this.$store.getters["posts/all"].length) {
                await this.$store.dispatch('posts/load')
            }
        }
    }
</script>
