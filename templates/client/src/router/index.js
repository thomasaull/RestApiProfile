import Vue from 'vue'
import Router from 'vue-router'
import Login from '@/components/Login'
import Content from '@/components/Content'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/',
      name: 'Content',
      component: Content
    }
  ]
})
