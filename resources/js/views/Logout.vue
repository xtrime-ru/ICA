<template>
    <div class="form-wrapper" >
        <h1 class="primary--text">Выход...</h1>
    </div>
</template>


<script>
    export default {
        props: {
            redirect: String
        },
        async created() {
            console.log(this.redirect)
            //await this.$store.dispatch('user/logout')
            if (!this.redirect) {
                this.$router.back()
            } else {
                this.$router.push({path: this.redirect})
            }
        }
    }
</script>
