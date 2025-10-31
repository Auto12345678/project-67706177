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

  {
    path: '/student',
    name: 'student',
    component: () => import('../views/Student.vue')
  },

  {
    path: '/add_student',
    name: 'add_student',
    component: () => import('../views/Add_student.vue')
  },

  {
    path: '/edit',
    name: 'edit',
    component: () => import('../views/Edit_customer.vue')
  },

  {
    path: '/edit_product',
    name: 'edit_product',
    component: () => import('../views/product_edit.vue')
  },

  {
    path: '/employees',
    name: 'employees',
    component: () => import('../views/Employees.vue')
  },

  {
    path: '/login_custom',
    name: 'login_custom',
    component: () => import('../views/login_customer.vue')
  },

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

// 🧠 Navigation Guard — ตรวจสอบการเข้าสู่ระบบ
router.beforeEach((to, from, next) => {
  const isLoggedIn = localStorage.getItem("customerLogin") === "true";

  // ถ้าหน้านั้นต้องล็อกอินก่อน แต่ยังไม่ได้ล็อกอิน
  if (to.meta.requiresAuth && !isLoggedIn) {
    alert("⚠ กรุณาเข้าสู่ระบบก่อนใช้งานหน้านี้");
    next("/login_customer");
  }
  // ถ้าเข้าสู่ระบบแล้วแต่พยายามกลับไปหน้า login อีก → ส่งกลับหน้าแรก
  else if (to.path === "/login" && isLoggedIn) {
    next("/show");
  } 
  // อื่น ๆ ไปต่อได้ตามปกติ
  else {
    next();
  }
});

export default router
