<template>
  <v-container class="fill-height">
    <h1 class="primary--text">Лента</h1>

      <v-alert
              v-if="posts.length === 0 && !hasMorePosts"
              outlined
              type="warning"
              color="primary"
              class="flex"
      >
          <div>Нет постов :(</div>
      </v-alert>

    <v-row>

        <v-col
                v-if="loading"
                v-for="item in 10"
                :key="'sk'+item"
                :cols="12"
                style="width: 100%"
        >
            <v-skeleton-loader
                    class="post"
                    type="list-item-avatar, image, list-item-two-line, actions"
            ></v-skeleton-loader>
        </v-col>
      <transition name="fade">
          <div class="transition-wrapper" v-if="!loading" >
            <v-col
              v-for="post in posts"
              :key="post.id"
              :cols="12"
          >
            <v-card
                    class="post fade-enter-active"
                    :link=true
                    target="_blank"
                    :href="post.url"
            >
              <v-item-group class="post-info">
                <a :href="post.source.url" target="_blank">
                  <img v-if="post.source.icon_url"
                       :alt="post.source.name"
                       :src="post.source.icon_url"
                  />
                  <span v-if="!post.source.icon_url" >{{post.source.name}}</span>
                </a>
                <span>{{ post.created_at | moment("from", "now") }}</span>
              </v-item-group>
              <v-card-title v-if="post.title" v-text="post.title"/>
              <v-img
                  v-if="post.image"
                  :src="post.image"
                  :eager="true"
              />
              <v-card-text class="post-text" v-if="post.description">{{ post.description }}</v-card-text>

              <v-card-actions
                  class="actions flex-fill d-flex justify-space-around"
                  @click.prevent
              >
                <v-item-group class="primary--text">
                  <v-btn icon @click="updateMeta('liked', post)" color="primary">
                    <v-icon v-show="!post.meta.liked">mdi-heart-outline</v-icon>
                    <v-icon v-show="post.meta.liked">mdi-heart</v-icon>
                  </v-btn>
                  <span>{{ post.meta.likes }}</span>
                </v-item-group>

                <v-item-group class="primary--text">
                  <v-btn icon @click="updateMeta('bookmarked', post)" color="primary">
                    <v-icon v-show="!post.meta.bookmarked">mdi-bookmark-outline</v-icon>
                    <v-icon v-show="post.meta.bookmarked">mdi-bookmark</v-icon>
                  </v-btn>
                  <span>{{ post.meta.bookmarks }}</span>
                </v-item-group>

                <v-item-group class="primary--text">
                  <v-btn icon
                         @click="openLink(post.url); updateMeta('viewed', post, true);"
                         v-intersect.once="onIntersectPost"
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
          </div>
      </transition>
    </v-row>

    <div
            v-show="hasMorePosts && !loading"
            v-intersect="{
              handler: onIntersectLoader,
              options: {
                threshold: 0.95
              }
            }"
            class="infinity_loader"
    >
        <div class="loader_msg">
            <span  v-if="touch"  class="mdi mdi-gesture-swipe-down"></span>
            <span  v-if="!touch"  class="mdi mdi-mouse-move-down"></span>
            <p>{{infinityLoaderText}}</p>
        </div>

    </div>
  </v-container>
</template>

<script>
import {mapState} from "vuex"

export default {
  data() {
    return {
      touch: false,
      infinityLoaderText: '',
    }
  },
  computed: mapState({
    posts: state => state.posts.posts,
    hasMorePosts: state => state.posts.hasMorePosts,
    loading: state => state.posts.loading,
  }),
  methods: {
    fetchData(reload = true) {
      return this.$store.dispatch("posts/load", reload);
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
    onIntersectPost (entries, observer, isIntersected) {
      if (!isIntersected) {
        return;
      }
      let postId = entries[0].target.getAttribute("postId")
      let post = this.$store.getters["posts/get"](postId)

      if (!post.meta.viewed) {
        this.updateMeta('viewed', post, true)
      }
    },
    onIntersectLoader (entries, observer) {
      if (entries[0].intersectionRatio >= 0.9) {
        this.$store.commit("posts/setLoading", true);

        Promise.all([
            this.fetchData(false),
            this.$vuetify.goTo(0, {
              duration: 750,
              offset: 0,
              easing: "easeInOutQuart",
            }),
        ]).then(
            () => this.$store.commit("posts/setLoading", false)
        );
      }
    }
  },
  async created() {
    this.fetchData(true).then(() => this.$store.commit("posts/setLoading", false));

    this.touch = ('ontouchstart' in window);
    if (this.touch) {
      this.infinityLoaderText = 'Потяните вниз, что бы открыть следующую страницу.';
    } else {
      this.infinityLoaderText = 'Прокрутите вниз, что бы открыть следующую страницу.';
    }
  },
  watch: {
    '$route': 'fetchData'
  }
}
</script>

<style lang="scss" scoped>
  .fade-enter-active, .fade-leave-active {
    transition: opacity 750ms;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }

  .row {
    overflow: hidden;
    min-height: 100vh;

    .transition-wrapper {
      width: 100%;
    }
  }
  .post {
    .post-info {
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

    .actions {
      cursor: default;
      max-width: 600px;
      margin: 0 auto;
      padding: 2em 0;

      & .v-btn {
        vertical-align: sub;
      }
    }
    .post-text {
      white-space: pre-wrap;
      padding-bottom: 0;
      //font-size: 0.9rem;
    }
  }

  .infinity_loader {
    width: 100%;
    height: 100vh;
    text-align: center;

    .loader_msg {
      padding-top: 4em;

      span {
        display: inline-block;

        font-size: 4em;
        animation: animation-bounce 1000ms linear infinite;
      }

      @keyframes animation-bounce {
        0%, 100% {
          transform: translateY(0);
        }
        10%,90%{
          transform: translateY(-5px);
        }
        40%, 80% {
          transform: translateY(-30px);
        }
        50% {
          transform: translateY(-55px);
        }
        60%{
          transform: translateY(-60px);
        }
      }
    }
  }
</style>
