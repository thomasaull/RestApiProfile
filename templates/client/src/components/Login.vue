<template>
  <div class="login">
    <h2>Login</h2>
    <form class="login-form">
      <label for="username">Username:</label>
      <input type="text" name="username" v-model="username"/>

      <label for="password">Password:</label>
      <input type="password" name="password" v-model="password"/>
      <button @click.prevent="login">Login</button>
    </form>
  </div>
</template>

<script>

import axios from 'axios'

export default {
  name: 'Login',

  data () {
    return {
      loading: false,
      username: undefined,
      password: undefined
    }
  },

  methods: {
    login () {
      this.loading = true
      axios.post('auth', {
        username: this.username,
        password: this.password
      })
      .then(response => {
        if (response.data.hasOwnProperty('jwt')) {
          this.$store.commit('setJWT', response.data.jwt)
          this.$router.push('/')
        }
      })
      .catch(error => {
        console.log(error.response.data)
      })
    }
  }
}
</script>

<style>

.login {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: left;
}

.login-form {
}

.login-form input {
  display: block;
  margin-bottom: 10px;
}

</style>
