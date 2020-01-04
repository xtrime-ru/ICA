<template>
    <div class="form-wrapper">
        <h1 class="primary--text">Регистрация</h1>
        <v-form
            ref="form"
            v-model="valid"
        >
            <v-text-field
                v-model="user.name"
                :counter="fieldLimit"
                :rules="[rules.username, rules.required, rules.textLength]"
                autocomplete="username"
                label="Name"
                required
            />
            <v-text-field
                v-model="user.email"
                :counter="fieldLimit"
                :rules="[rules.required, rules.textLength, rules.email]"
                autocomplete="email"
                label="Email"
                required
            />
            <v-text-field
                v-model="user.password"
                :counter="fieldLimit"
                :rules="[rules.required, rules.passwordLength]"
                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show1 ? 'text' : 'password'"
                autocomplete="new-password"
                label="Пароль"
                required
                @click:append="show1 = !show1"
            />
            <v-text-field
                v-model="user.password_confirmation"
                :counter="fieldLimit"
                :rules="[rules.required, rules.passwordLength, passwordConfirmationRule]"
                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show1 ? 'text' : 'password'"
                autocomplete="new-password"
                label="Повторите пароль"
                required
                @click:append="show1 = !show1"
            />
            <v-btn class="mr-4" :disabled="!valid" @click="submit">Зарегестрироваться</v-btn>
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
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
            },
            rules: {
                'username': v => /^[\wа-я\d\._ -]*$/iu.test(v) || 'Недопустимые символы',
                'required': v => !!v || 'Обязательно поле',
                'textLength': v => (v && v.length <= maxFieldLimit && v.length >= minFieldLimit) || `Длина должна быть ${minFieldLimit} до ${maxFieldLimit} символов`,
                'passwordLength': v => (v && v.length <= maxFieldLimit && v.length >= minPasswordLimit) || `Длина должна быть ${minPasswordLimit} до ${maxFieldLimit} символов`,
                'email': v => /.+@.+\..+/.test(v) || 'E-mail некорректен',
            },
        }),
        computed: {
            passwordConfirmationRule() {
                return () => (this.user.password === this.user.password_confirmation) || 'Пароли должны совпадать'
            },
        },
        methods: {
            submit() {
                if (this.$refs.form.validate()) {
                    this.$store.dispatch('user/register', this.user)
                }
            },
        }
    }
</script>
