<template>
  <div class="container d-flex">
    <div class="form-wrapper align-self-center">
      <h1 class="primary--text">Сбросить пароль</h1>
      <v-form
          ref="form"
          v-model="valid"
          :disabled=isDisabled
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
            v-if="user.token"
            v-model="user.password"
            :counter="passwordLimit"
            :rules="[rules.required, rules.passwordLength]"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append="show1 = !show1"
            :type="show1 ? 'text' : 'password'"
            autocomplete="new-password"
            label="Новый пароль"
            required
            :error-messages="getErrors('password')"
            v-on:input="resetErrors"
        />
        <v-text-field
            v-if="user.token"
            v-model="user.password_confirmation"
            :counter="passwordLimit"
            :rules="[rules.required, rules.passwordLength, passwordConfirmationRule]"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append="show1 = !show1"
            :type="show1 ? 'text' : 'password'"
            autocomplete="new-password"
            label="Повторите новый пароль"
            required
            :error-messages="getErrors('password_confirmation')"
            v-on:input="resetErrors"
        />

        <v-btn class="my-4 mr-0 ml-auto d-flex" primary--text :disabled="!valid" @click="submit">
          {{this.user.token ? "Сбросить" : "Отправить письмо"}}
        </v-btn>

      </v-form>
    </div>
  </div>
</template>

<script>
const maxFieldLimit = 100
const minFieldLimit = 3
const minPasswordLimit = 8
const maxPasswordLimit = 32

export default {
  data: () => ({
    props: {
      token: String,
      email: String,
    },
    show1:false,
    fieldLimit: maxFieldLimit,
    passwordLimit: maxPasswordLimit,
    valid: true,
    isDisabled: false,
    user: {
      email: "",
      token: "",
      password: "",
      password_confirmation: "",
    },
    errors: [],
    rules: {
      "required": v => !!v || "Обязательно поле",
      "textLength": v => (v && v.length <= maxFieldLimit && v.length >= minFieldLimit) || `Длина должна быть ${minFieldLimit} до ${maxFieldLimit} символов`,
      "passwordLength": v => (v && v.length <= maxPasswordLimit && v.length >= minPasswordLimit) || `Длина должна быть ${minPasswordLimit} до ${maxPasswordLimit} символов`,
      "email": v => /.+@.+\..+/.test(v) || "E-mail некорректен",
    },
  }),
  created: function () {
    this.user.email = this.$route.query.email;
    this.user.token = this.$route.params.token;
  },
  computed: {
    passwordConfirmationRule() {
      return () => (this.user.password === this.user.password_confirmation) || "Пароли должны совпадать"
    },
  },
  methods: {
    submit() {
      if (this.$refs.form.validate()) {
        this.isDisabled = true;
        const url = this.user.token ? "/password/reset" : "/password/email"
        const data = this.user.token
            ? this.user
            : {email: this.user.email}
        this.$http.post(url, data).then(
            (response) => {
              this.$store.commit("notifications/add", {
                text: response.body.message,
                timeout: 0,
                color: "success"
              })
              let path = this.$route.query.redirect || "/login"
              this.$router.push({path: path})
            },
            (errors) => {
              this.isDisabled = false;
              this.errors = errors.body.errors
              this.$store.commit("notifications/add", {
                text: errors.body.message,
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
