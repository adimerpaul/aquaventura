import MainLayout from 'layouts/MainLayout.vue'
import IndexPage from 'pages/IndexPage.vue'
import Login from 'pages/Login.vue'
import Cards from 'pages/Cards.vue'
import CardsRegister from 'pages/CardsRegister.vue'

const routes = [
  {
    path: '/',
    component: MainLayout,
    children: [
      { path: '', component: IndexPage },
      { path: 'cards', component: Cards },
      { path: 'cardsRegister', component: CardsRegister }
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
