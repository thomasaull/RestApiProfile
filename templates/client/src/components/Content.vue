<template>

<div class="content">
  <h1>Content</h1>
  User logged in: {{ user }}<br>
  <template v-if="loggedOut">User is now logged out, on reloading this page you should be redirected to /login <br></template>
  <button @click.prevent="logout">Logout</button>
</div>

</template>

<script>

import axios from 'axios'

export default {
  name: 'Content',

  data () {
    return {
      user: undefined,
      loggedOut: false
    }
  },

  mounted () {
    axios.get('/test').then(result => {
      this.user = result.data.user
    })
  },

  methods: {
    logout () {
      axios.delete('auth').then(() => {
        this.loggedOut = true
      })
    }
  }
}
</script>

<style>

.content {

}

</style>
