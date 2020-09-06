<template>
  <v-container>
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
          v-for="post in posts"
          :key="post.id"
          :cols="12"
      >
        <v-card class="post"
                :link=true
                target="_blank"
                :href="post.url">
          <v-card-title v-text="post.title"
          />
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
                     color="primary">
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
import {mapActions, mapState} from "vuex"

export default {
  computed: mapState({
    posts: state => state.posts.posts,
    hasMorePosts: state => state.posts.hasMorePosts
  }),
  methods: {
    ...mapActions("posts", [
      "load",
    ]),
    updateMeta: function (property, post, value) {
      if (value === undefined) {
        post.meta[property] = !post.meta[property]
      } else {
        post.meta[property] = value
      }

      this.$store.dispatch("posts/updateMeta", post.meta)
    },
    openLink: function(url) {
      window.open(url, '_blank');
    }
  },
  async created() {
    if (!this.$store.getters["posts/all"].length) {
      await this.$store.dispatch("posts/load")
    }
  }
}
</script>

<style>


.post .actions {
  cursor: default;
  max-width: 600px;
  margin: 0 auto;
}

.post .actions .v-btn {
  vertical-align: sub;
}
</style>
