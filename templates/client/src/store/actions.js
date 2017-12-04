import axios from 'axios'

export const auth = (context, data) => {
  return axios.get('/auth')
  .then(response => {
    console.log('auth successful')
    context.commit('setJWT', response.data.jwt)
    return response
  })
  .catch(error => {
    console.log(error.response.data.error)
    throw error
  })
}
