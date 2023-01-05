
import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { useCounterStore } from 'stores/example-store'
import moment from 'moment'

// Be careful when using SSR for cross-request state pollution
// due to creating a Singleton instance here;
// If any client changes this (global) instance, it might be a
// good idea to move this instance creation inside of the
// "export default () => {}" function below (which runs individually
// for each client)
const api = axios.create({ baseURL: process.env.API })

export default boot(({ app, router }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api
  app.config.globalProperties.$filters = {
    ageMonthDays: (value) => {
      const years = moment().diff(value, 'years')
      const months = moment().diff(value, 'months')
      const months2 = months - (years * 12)
      const days = moment().diff(value, 'days')
      const days2 = days - (months * 30)
      return ` ${years} años ${months2} meses ${days2} días`
    },
    age: (value) => {
      const years = moment().diff(value, 'years')
      return ` ${years} años`
    }
  }
  app.config.globalProperties.$schedules = [
    '06:00-07:00',
    '07:00-08:00',
    '08:00-09:00',
    '09:00-10:00',
    '10:00-11:00',
    '11:00-12:00',
    '12:00-13:00'
  ]
  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file
  app.config.globalProperties.$api = api
  const token = localStorage.getItem('tokenAqua')

  if (token) {
    app.config.globalProperties.$api.defaults.headers.common.Authorization = `Bearer ${token}`
    app.config.globalProperties.$api.post('me').then(res => {
      useCounterStore().user = res.data
      useCounterStore().permissions = res.data.permissions.map(permission => permission.name)
      useCounterStore().isLoggedIn = true
    }).catch(err => {
      console.log(err)
      app.config.globalProperties.$api.defaults.headers.common.Authorization = ''
      useCounterStore().user = {}
      localStorage.removeItem('tokenAqua')
      useCounterStore().isLoggedIn = false
      router.push('/login')
    })
  } else {
    router.push('/login')
    useCounterStore().user = {}
    localStorage.removeItem('tokenAqua')
    useCounterStore().isLoggedIn = false
  }
  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { api }
