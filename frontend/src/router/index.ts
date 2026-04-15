import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import ProductsView from '../views/ProductsView.vue'
import UsersView from '../views/UsersView.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
     path: '/users',
     component: UsersView,
     meta: { requiresAuth: true },
},
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
    {
      path: '/',
      name: 'products',
      component: ProductsView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to, _from, next) => {
  const token = localStorage.getItem('token')

  
  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  
  if ((to.path === '/login' || to.path === '/register') && token) {
    return next('/')
  }

  next()
})

export default router