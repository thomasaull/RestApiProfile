// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import axios from 'axios'
import App from '@/App'
import router from '@/router'
import store from '@/store'

Vue.config.productionTip = false

// axios api default url
axios.defaults.baseURL = window.location.origin + '/api'

// Check each route for Auth
router.beforeEach((to, from, next) => {
  // bypass auth check for login route
  if (to.path === '/login') {
    next()
    return
  }

  // check for jwt token and if not present, get it first
  if (store.state.jwt) {
    // console.log('next because jwt set')
    next()
  } else {
    // console.log('dispatch auth')
    store.dispatch('auth')
    .then(() => {
      // console.log('next after auth')
      next()
    })
    .catch(() => {
      // store.commit('setError', 'Authentification was not sucessful')
      // console.log('redirect to login')
      router.push('/login')
    })
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})
