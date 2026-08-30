import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    component: () => import('../layouts/GuestLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('../views/HomeView.vue'),
        meta: { requiresGuest: false },
      },
      {
        path: 'login',
        name: 'login',
        component: () => import('../views/LoginView.vue'),
        meta: { requiresGuest: true },
      },
      {
        path: 'register',
        name: 'register',
        component: () => import('../views/RegisterView.vue'),
        meta: { requiresGuest: true },
      },
    ],
  },
  {
    path: '/app',
    component: () => import('../layouts/AppLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('../views/DashboardView.vue'),
      },
      {
        path: 'projects',
        name: 'projects',
        component: () => import('../views/ProjectsView.vue'),
      },
      {
        path: 'projects/:projectId/board',
        name: 'project-board',
        component: () => import('../views/KanbanBoardView.vue'),
      },
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router