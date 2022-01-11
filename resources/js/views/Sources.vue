<template>
  <v-container class="fill-height full-width">
    <h1 class="primary--text">Источники</h1>

    <v-row
        class="justify-center"
    >
      <v-col
          v-if="loading"
          v-for="item in 12"
          :key="item"
          class="flex-grow-0"
      >
        <v-skeleton-loader
            width="350px"
            type="list-item-avatar-two-line, actions"
        >
        </v-skeleton-loader>
      </v-col>

      <v-col
          v-if="!loading"
          v-for="category in categories"
          :key="category.id"
          cols="12"
          class="justify-center"
      >
        <h2 class="font-weight-light text-center">
          <a  :id="category.slug" :href="'#' + category.slug" >#</a> {{category.name}}
        </h2>
        <v-spacer></v-spacer>
        <v-col
            v-for="source in getByCategory(category.id)"
            :key="source.id"
            class="d-flex flex-grow-0"
        >
          <v-card
              width="350px"
              class="card ma-2"
          >
            <v-card-title>
              {{source.name}}
            </v-card-title>
            <v-card-actions>
              <v-switch
                  :input-value="isEnabled(source.id)"
                  @change="toggleSource(source.id, !isEnabled(source.id))"
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
  },
  created() {
    this.fetchData()
  }
}
</script>

<style scoped>
  .full-width {
    max-width: initial!important;
    width: auto;
  }
  a {
    text-decoration-line: none;
  }
</style>