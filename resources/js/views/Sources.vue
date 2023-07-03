<template>
  <v-container class="fill-height full-width">
    <h1 class="primary--text">Источники</h1>

    <v-row>
      <v-col
          v-if="loading"
          class="flex-wrap d-flex align-content-space-between"
      >
        <v-skeleton-loader
            type="list-item"
            class="text-center full-width"
            style="padding: 0.5rem 40% 1.5rem"
        ></v-skeleton-loader>
        <v-col
            v-for="item in 24"
            :key="item"
        >
          <v-skeleton-loader
              class="source-card"
              type="list-item-avatar-two-line, actions"
          >
          </v-skeleton-loader>
        </v-col>
      </v-col>

      <v-col
          v-if="!loading"
          v-for="category in categories"
          :key="category.id"
          class="flex-wrap d-flex align-content-space-between my-2"
          cols="12"
          :id="category.slug"
      >
        <h2 class="font-weight-light text-center full-width font-weight-bold pa-2 pb-5">
          <a :href="'#' + category.slug" >#</a> {{category.name}}
        </h2>
        <v-col
            v-for="source in getByCategory(category.id)"
            :key="source.id"
        >
          <v-card class="source-card" :ripple="true" @click="$refs[source.id][0].$refs.input.click()">
            <a :href="source.url" target="_blank">
              <v-card-title class="flex-nowrap justify-space-between" :title="source.name">
                <img
                    v-if="source.icon_url"
                    :src="source.icon_url"
                />
                {{source.name | truncate(15, '...')}}
              </v-card-title>
            </a>

            <v-card-text class="py-0">
              <v-chip-group>
                <v-chip class="chip-invisible"></v-chip>
                <v-chip v-if="source.social">{{source.social}}</v-chip>
                <v-chip v-if="source.age_limit > 0" :color="source.age_limit === 16 ? 'orange' : source.age_limit === 18 ? 'red' : 'none'">{{source.age_limit + '+'}}</v-chip>
              </v-chip-group>
            </v-card-text>

            <v-card-actions
                class="justify-space-between"
                @click.stop
            >
              <v-item-group class="counters">
                <v-icon>mdi-heart-outline</v-icon>
                <span>{{ source.likes | abbreviate }}</span>
              </v-item-group>
              <v-item-group class="counters">
                <v-icon >mdi-eye-outline</v-icon>
                <span>{{ source.views | abbreviate }}</span>
              </v-item-group>
              <v-switch
                  :inset=true
                  color="green"
                  :input-value="isEnabled(source.id)"
                  @change="toggleSource(source.id, !isEnabled(source.id))"
                  :ref="source.id"
              ></v-switch>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-col>


    </v-row>
  </v-container>
</template>

<script>
import {mapState, mapGetters} from "vuex"

export default {
  computed: {
    ...mapState({
      categories: state => state.sources.categories,
      sources: state => state.sources.sources,
      loading: state => state.sources.loading,
    }),
    ...mapGetters({
      isEnabled: 'sources/isEnabled',
      getByCategory: 'sources/getByCategory'
    })
  },
  methods: {
    fetchData() {
      this.$store.dispatch("sources/load")
      window.scrollTo(0,0)
    },
    toggleSource: function (sourceId, enabled) {
      this.$store.dispatch("sources/toggleSource", {sourceId, enabled})
    },
    log: function ($el) {
      console.log($el);
    }
  },
  created() {
    this.fetchData()
  }
}
</script>

<style scoped>
  .full-width {
    max-width: initial !important;
    width: auto;
  }

  a {
    text-decoration-line: none;
  }

  .source-card {
    margin: auto;
    min-width: 300px;
    max-width: 400px;
    flex-grow: 1;
    align-self: center;
  }

  .source-card > a {
    color: inherit;
  }

  .source-card > .v-card__text {
    padding-bottom: 0;
  }

  .source-card > .v-card__actions {
    padding: 0 8px 0 16px;
  }

  .v-card__title img {
    height: 2rem;
  }

  .counters, .counters .v-icon {
    color: #74787e;
  }

  .source-card .chip-invisible {
    width: 0;
    padding: 0;
    margin-left: 0;
    margin-right: 0;
  }
</style>