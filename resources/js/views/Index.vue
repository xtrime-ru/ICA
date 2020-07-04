<template>
    <v-container>
        <h1 class="primary--text">Лента</h1>

        <v-alert
                v-if="posts.length === 0"
                outlined
                type="warning"
                color="primary"
        >
            <div>Нет постов :(</div>
        </v-alert>

        <v-row dense>
            <v-col
                    v-for="post in posts"
                    :key="post.id"
                    :cols="12"
            >
                <v-card>
                    <v-card-title v-text="post.title"></v-card-title>
                    <v-card-actions class="flex-fill d-flex justify-space-around justify-sm-end">
                        <v-btn icon >
                            <v-icon>mdi-heart</v-icon>
                        </v-btn>

                        <v-btn icon>
                            <v-icon>mdi-bookmark</v-icon>
                        </v-btn>

                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-btn
                v-if="hasMorePosts"
                @click="load"
                block
                dark
                class="mt-10"
                >
            Load more
        </v-btn>
    </v-container>
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
