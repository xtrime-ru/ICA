<template>
  <v-container class="fill-height">
    <h1 class="primary--text">Лента</h1>

    <v-alert
        v-if="posts.length === 0 && !hasMorePosts"
        outlined
        type="warning"
        color="primary"
    >
      <div>Нет постов :(</div>
    </v-alert>



    <v-row dense>
      <v-col
          v-if="loading"
          v-for="item in 3"
          :key="item"
          :cols="12"
          class="my-2"
      >
        <v-skeleton-loader
            class="post"
            type="list-item-avatar, image, list-item-two-line, actions"
        ></v-skeleton-loader>
      </v-col>

      <v-col
          v-if="!loading"
          v-for="post in posts"
          :key="post.id"
          :cols="12"
          class="my-2"
      >
        <v-card class="post"
                :link=true
                target="_blank"
                :href="post.url"
        >
          <v-item-group class="post-info">
            <a :href="post.source.url" target="_blank">
              <img v-if="post.source.icon"
                   :alt="post.source.name"
                   :src="post.source.icon"
              />
              <span v-if="!post.source.icon" >{{post.source.name}}</span>
            </a>
            <span>{{ post.created_at | moment("from", "now") }}</span>
          </v-item-group>
          <v-card-title v-if="post.title" v-text="post.title"/>
          <v-img
              v-if="post.image"
              :src="post.image"
              :eager="true"
          />
          <v-card-text>
            {{ post.description }}
          </v-card-text>

          <v-card-actions
              class="actions flex-fill d-flex justify-space-around"
              v-on:click.prevent
          >
            <v-item-group class="primary--text">
              <v-btn icon v-on:click="updateMeta('liked', post)" color="primary">
                <v-icon v-show="!post.meta.liked">mdi-heart-outline</v-icon>
                <v-icon v-show="post.meta.liked">mdi-heart</v-icon>
              </v-btn>
              <span>{{ post.meta.likes }}</span>
            </v-item-group>

            <v-item-group class="primary--text">
              <v-btn icon v-on:click="updateMeta('bookmarked', post)" color="primary">
                <v-icon v-show="!post.meta.bookmarked">mdi-bookmark-outline</v-icon>
                <v-icon v-show="post.meta.bookmarked">mdi-bookmark</v-icon>
              </v-btn>
              <span>{{ post.meta.bookmarks }}</span>
            </v-item-group>

            <v-item-group class="primary--text">
              <v-btn icon
                     v-on:click="openLink(post.url); updateMeta('viewed', post, true);"
                     v-intersect.once="onIntersect"
                     color="primary"
                     :postId="post.id"
              >
                <v-icon v-show="!post.meta.viewed">mdi-eye-outline</v-icon>
                <v-icon v-show="post.meta.viewed">mdi-eye</v-icon>
              </v-btn>
              <span>{{ post.meta.views }}</span>
            </v-item-group>

          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <v-btn
        v-if="hasMorePosts && !loading"
        @click="fetchData(false)"
        block
        dark
        x-large
        class="mt-10 primary--text"
    >
      Load more
    </v-btn>
  </v-container>
</template>

<script>
import {mapState, mapGetters} from "vuex"

export default {
  computed: mapState({
    posts: state => state.posts.posts,
    hasMorePosts: state => state.posts.hasMorePosts,
    loading: state => state.posts.loading
  }),
  methods: {
    fetchData(reload = true) {
      this.$store.dispatch("posts/load", reload);
      window.scrollTo(0,0);
    },
    updateMeta: function (property, post, value) {
      if (value === undefined) {
        value = !post.meta[property]
      }
      this.$store.dispatch("posts/updateMeta", {postId:post.id, property:property, value:value})
    },
    openLink: function(url) {
      window.open(url, '_blank');
    },
    onIntersect (entries, observer, isIntersected) {
      if (!isIntersected) {
        return;
      }
      let postId = entries[0].target.getAttribute("postId")
      let post = this.$store.getters["posts/get"](postId)

      if (!post.meta.viewed) {
        this.updateMeta('viewed', post, true)
      }
    }
  },
  created() {
    this.fetchData(true);
  },
  watch: {
    '$route': 'fetchData'
  }
}
</script>

<style lang="scss">

.row {
  overflow: hidden;
}
.post .post-info {
  width: 100%;
  padding: 1em 1em 0;
  display: flex;
  justify-content: space-between;

  & img {
    border-radius: 2px;
    height: 2em;
    width: auto;
  }
}

.post .actions {
  cursor: default;
  max-width: 600px;
  margin: 0 auto;

  & .v-btn {
    vertical-align: sub;
  }
}
</style>
