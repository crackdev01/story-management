import { createRouter, createWebHistory } from 'vue-router'

import store from '../store/store';
import httpRequest from '../helper/HttpRequest';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/Login.vue')
    },
    {
      path: '/',
      name: 'dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/story/:id',
      name: 'story',
      component: () => import('../views/Story.vue'),
      meta: { requiresAuth: true },
    },
  ]
})

router.beforeEach(async (to, from, next) => {
  // Check for requiresAuth guard and login path if user is not loaded
  if ((to.meta.requiresAuth || to.path === "/login") && !store.state.userLoaded) {
    try {
      // Fetch user data from /me API
      const response = await httpRequest.get('/me');
      const user = response.data;

      // Update the user state in Vuex store
      store.commit('setUser', user);

      // Redirect to dashboard if user tries to access login page and user is authenticated
      if(to.path === '/login'){
        next('/')
      }else{
        next();
      }
    } catch (error) {
        // Update the user state in Vuex store
        store.commit('setUser', null);

        if(to.path === '/login'){
          next()
        }else{
          // Redirect to login page if user is not authenticated and trying to access a restricted page
          next('/login');
        }
    }
  }
  // Redirect to dashboard if user tries to access login page and user is authenticated
  else if(to.path === '/login' && store.state.user){
    next('/');
  }else{ 
    next();
  }
});

export default router
