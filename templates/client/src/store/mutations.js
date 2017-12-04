import axios from 'axios'

export const setJWT = (state, jwt) => {
  axios.defaults.headers.common['Authorization'] = 'Bearer' + jwt
  state.jwt = jwt
}
