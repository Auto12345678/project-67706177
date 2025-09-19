import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue')
  }
,
{
    path: '/show',
    name: 'show',
    component: () => import('../views/ShowProduct.vue')
  },

  {
    path: '/custom',
    name: 'custom',
    component: () => import('../views/Customer.vue')
  },

  {
    path: '/add_custom',
    name: 'add_custom',
    component: () => import('../views/Add_customer.vue')
  },
  
  {
    path: '/product',
    name: 'product',
    component: () => import('../views/Product.vue')
  },

  {
    path: '/add_product',
    name: 'add_product',
    component: () => import('../views/Add_product.vue')
  },

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
