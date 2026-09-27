// src/router/index.ts
import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes: Array<RouteRecordRaw> = [
  // Public & Guest Routes (Wrapped in GuestLayout)
  {
    path: '/',
    component: () => import('../layouts/GuestLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('../views/HomeView.vue'),
      },
      {
        path: 'about',
        name: 'about',
        component: () => import('../views/AboutView.vue'),
      },
      {
        path: 'how-it-works',
        name: 'how-it-works',
        component: () => import('../views/HowItWorksView.vue'),
      },
      {
        path: 'contact',
        name: 'contact',
        component: () => import('../views/ContactView.vue'),
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
      {
        path: 'activate',
        name: 'activate',
        component: () => import('../views/ActivateView.vue'),
        meta: { requiresGuest: true },
      },
    ],
  },

  // Private Application Routes (Wrapped in AppLayout/AuthenticatedLayout)
  {
    path: '/app',
    component: () => import('../layouts/AuthenticatedLayout.vue'), // Or '../layouts/AppLayout.vue'
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

  // Fallback Wildcard Route (Redirects unknown URLs back to Home)
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

// Navigation Guard for Authentication and Guest Checks
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // Ensure current user state is checked if token exists in localStorage
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router