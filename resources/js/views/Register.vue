<template>
  <div class="container d-flex">

    <div class="form-wrapper align-self-center">
      <h1 class="primary--text">Регистрация</h1>
      <v-form
          ref="form"
          v-model="valid"
          @keydown.enter.native="submit"
      >
        <v-text-field
            v-model="user.name"
            :counter="fieldLimit"
            :rules="[rules.username, rules.required, rules.textLength]"
            autocomplete="username"
            label="Name"
            required
            :error-messages="getErrors('name')"
            v-on:input="resetErrors"
        />
        <v-text-field
            v-model="user.email"
            :counter="fieldLimit"
            :rules="[rules.required, rules.textLength, rules.email]"
            autocomplete="email"
            label="Email"
            hint="На него будет отправлено письмо для активации аккаунта"
            :hide-details=false
            required
            :error-messages="getErrors('email')"
            v-on:input="resetErrors"
        />
        <v-text-field
            v-model="user.password"
            :counter="fieldLimit"
            :rules="[rules.required, rules.passwordLength]"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append="show1 = !show1"
            :type="show1 ? 'text' : 'password'"
            autocomplete="new-password"
            label="Пароль"
            required
            :error-messages="getErrors('password')"
            v-on:input="resetErrors"
        />
        <v-text-field
            v-model="user.password_confirmation"
            :counter="fieldLimit"
            :rules="[rules.required, rules.passwordLength, passwordConfirmationRule]"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append="show1 = !show1"
            :type="show1 ? 'text' : 'password'"
            autocomplete="new-password"
            label="Повторите пароль"
            required
            :error-messages="getErrors('password_confirmation')"
            v-on:input="resetErrors"
        />
        <v-btn class="my-4 mr-0 ml-auto d-flex" :disabled="!valid" @click="submit">Зарегестрироваться</v-btn>
      </v-form>
    </div>
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
    errors: [],
    user: {
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
    },
    rules: {
      "username": v => /^[\wа-я\d\._ -]*$/iu.test(v) || "Недопустимые символы",
      "required": v => !!v || "Обязательно поле",
      "textLength": v => (v && v.length <= maxFieldLimit && v.length >= minFieldLimit) || `Длина должна быть ${minFieldLimit} до ${maxFieldLimit} символов`,
      "passwordLength": v => (v && v.length <= maxFieldLimit && v.length >= minPasswordLimit) || `Длина должна быть ${minPasswordLimit} до ${maxFieldLimit} символов`,
      "email": v => /.+@.+\..+/.test(v) || "E-mail некорректен",
    },
  }),
  computed: {
    passwordConfirmationRule() {
      return () => (this.user.password === this.user.password_confirmation) || "Пароли должны совпадать"
    },
  },
  methods: {
    submit() {
      if (this.$refs.form.validate()) {
        this.$store.dispatch("user/register", this.user).then(
            (response) => {
              this.$router.push("/login")
              this.$store.commit("notifications/add", {
                text: "Регистрация завершена. На email отправлена ссылка для активации аккаунта.",
                timeout: -1,
                color: "info"
              })
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
