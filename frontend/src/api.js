import axios from 'axios'
const api = axios.create({ baseURL: (import.meta.env.VITE_API_URL || 'http://69.62.76.226:8000') + '/api' })
api.interceptors.request.use(c => {
  const t = localStorage.getItem('token')
  if (t) c.headers.Authorization = `Bearer ${t}`
  c.headers.Accept = 'application/json'
  return c
})
export default api
