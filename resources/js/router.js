import { createRouter, createWebHistory } from 'vue-router';

// We will map Next.js routes to Vue components
const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('./pages/LandingPage.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('./pages/RegisterPage.vue')
  },
  {
    path: '/success',
    name: 'Success',
    component: () => import('./pages/SuccessPage.vue')
  },
  // Admin routes
  {
    path: '/x-ctrl-7d3k',
    component: () => import('./layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('./pages/admin/DashboardPage.vue')
      },
      {
        path: 'login',
        name: 'AdminLogin',
        component: () => import('./pages/admin/LoginPage.vue')
      },
      {
        path: 'registrations',
        name: 'AdminRegistrations',
        component: () => import('./pages/admin/RegistrationsPage.vue')
      },
      {
        path: 'reports',
        name: 'AdminReports',
        component: () => import('./pages/admin/ReportsPage.vue')
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: () => import('./pages/admin/SettingsPage.vue')
      }
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
