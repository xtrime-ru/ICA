<template>
    <v-container class="fill-height">
        <h1 class="text-error-title error--text mb-1">403</h1>
        <v-container>

        <p class="text-error-subtitle mb-6">Hold Up!</p>
        <p class="text-error-message mb-8 leading-normal">The government won't let us show you what's behind these doors&hellip;</p>
        <router-link :to="{ name: 'index'}" class="dim btn btn-lg btn-default btn-white text-90 no-text-shadow tracking-wide uppercase">
            Go Home
        </router-link>
    </v-container>
    </v-container>
</template>
<script setup lang="ts">
</script>