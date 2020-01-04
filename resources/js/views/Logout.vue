<template>
    <div class="form-wrapper" >
        <h1 class="primary--text">Выход</h1>
    </div>
</template>


<script>
    export default {
        created() {
            this.$store.dispatch('user/logout', this.user).then(
                async (response) => {
                    console.log('test')
                    await this.$router.back()
                },
                (error) => {
                    console.error(error)
                }
            )
        }
    }
</script>
