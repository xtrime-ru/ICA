<template>
    <v-container class="fill-height">
      <h1 class="primary--text">Настройки</h1>
      <v-container  class="fill-height">
        <v-btn
            color="red"
            block
            :to="{name: 'logout', params: {redirect:'/'}}">
          <v-icon left>
            mdi-logout
          </v-icon>
          Выйти из аккаунта
        </v-btn>
      </v-container>
    </v-container>
</template>

<script>
    export default {
    }
</script>
