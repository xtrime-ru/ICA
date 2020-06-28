<template>
    <div class="form-wrapper" >
        <h1 class="primary--text">Выход...</h1>
    </div>
</template>


<script>
    export default {
        async created() {
            await this.$store.dispatch('user/logout')
            this.$router.back()
        }
    }
</script>
