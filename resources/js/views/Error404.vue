<template>
    <div>
<h1 class="text-error-title font-normal mb-1">404</h1>
<p class="text-error-subtitle mb-6">Whoops&hellip;</p>
<p class="text-error-message mb-8 leading-normal">We're lost in space. The page you were trying to view does not exist.</p>
<router-link :to="{ name: 'index'}" class="dim btn btn-lg btn-default btn-white text-90 no-text-shadow tracking-wide uppercase">
    Go Home
</router-link>
    </div>
</template>
