<template>
  <div class="container d-flex">
    <div class="form-wrapper align-self-center">
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
            :counter="passwordLimit"
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

        <v-btn class="my-4 mr-0 ml-auto d-flex" primary--text :disabled="!valid" @click="submit">Войти</v-btn>

      </v-form>
    </div>
  </div>
</template>

<script>
const maxFieldLimit = 100
const minFieldLimit = 3
const maxPasswordLimit = 32
const minPasswordLimit = 6

export default {
  data: () => ({
    show1: false,
    fieldLimit: maxFieldLimit,
    passwordLimit: maxPasswordLimit,
    valid: true,
    user: {
      email: "",
      password: "",
    },
    errors: [],
    rules: {
      "required": v => !!v || "Обязательно поле",
      "textLength": v => (v && v.length <= maxFieldLimit && v.length >= minFieldLimit) || `Длина должна быть ${minFieldLimit} до ${maxFieldLimit} символов`,
      "passwordLength": v => (v && v.length <= maxPasswordLimit && v.length >= minPasswordLimit) || `Длина должна быть ${minPasswordLimit} до ${maxPasswordLimit} символов`,
      "email": v => /.+@.+\..+/.test(v) || "E-mail некорректен",
    },
  }),
  methods: {
    submit() {
      if (this.$refs.form.validate()) {
        this.$store.dispatch("user/login", this.user).then(
            (response) => {
              let path = this.$route.query.redirect || "/"
              this.$router.push({path: path})
            },
            (errors) => {
              this.errors = errors
              this.$store.commit("notifications/add", {
                text: errors,
                timeout: 0,
                color: "error"
              })
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
