import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Login from 'pages/Login.vue'
import Cards from 'pages/Cards.vue'
import CardsRegister from 'pages/CardsRegister.vue'
import History from 'pages/History.vue'
import Users from 'pages/Users.vue'

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage, meta: { requiresAuth: true } },
      { path: 'cards', component: Cards, meta: { requiresAuth: true } },
      { path: 'cardsRegister', component: CardsRegister, meta: { requiresAuth: true } },
      { path: 'history', component: History, meta: { requiresAuth: true } },
      { path: 'users', component: Users, meta: { requiresAuth: true } }
    ]
  },
  {
    path: '/login',
    component: Login
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
