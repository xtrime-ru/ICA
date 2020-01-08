<template>
    <div class="form-wrapper" >
        <h1 class="primary--text">Вход</h1>
        <v-form
            ref="form"
            v-model="valid"
            @keydown.enter.native="submit"
        >
            <v-text-field
                v-model="user.email"
                :counter="fieldLimit"
                :rules="[rules.required, rules.textLength, rules.email]"
                autocomplete="email"
                label="Email"
                required
                :error-messages="getErrors('email')"
                v-on:input="resetErrors"
            />
            <v-text-field
                v-model="user.password"
                :counter="fieldLimit"
                :rules="[rules.required, rules.passwordLength]"
                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show1 ? 'text' : 'password'"
                autocomplete="password"
                label="Пароль"
                required
                @click:append="show1 = !show1"
                :error-messages="getErrors('password')"
                v-on:input="resetErrors"
            />

            <v-layout row wrap justify-end mt-2>
                <v-flex shrink>
                    <v-btn primary--text :disabled="!valid" @click="submit">Войти</v-btn>
                </v-flex>
            </v-layout>

        </v-form>
    </div>
</template>

<script>
    const maxFieldLimit = 50
    const minFieldLimit = 3
    const minPasswordLimit = 8

    export default {
        data: () => ({
            show1: false,
            fieldLimit: maxFieldLimit,
            valid: true,
            user: {
                email: '',
                password: '',
            },
            errors:[],
            rules: {
                'required': v => !!v || 'Обязательно поле',
                'textLength': v => (v && v.length <= maxFieldLimit && v.length >= minFieldLimit) || `Длина должна быть ${minFieldLimit} до ${maxFieldLimit} символов`,
                'passwordLength': v => (v && v.length <= maxFieldLimit && v.length >= minPasswordLimit) || `Длина должна быть ${minPasswordLimit} до ${maxFieldLimit} символов`,
                'email': v => /.+@.+\..+/.test(v) || 'E-mail некорректен',
            },
        }),
        methods: {
            submit() {
                if (this.$refs.form.validate()) {
                    this.$store.dispatch('user/login', this.user).then(
                        (response) => {
                            let path = this.$route.query.redirect || '/'
                            this.$router.push({ path: path })
                        },
                        (errors) => {
                            this.errors = errors
                        }
                    )
                }
            },
            getErrors(name) {
                return this.errors[name] || [];
            },
            resetErrors() {
                this.errors = [];
            }
        }
    }
</script>
