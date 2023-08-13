// store/store.ts
import { createStore } from 'vuex';
import type { RootState, User } from '@/types';

// Define your state, mutations, actions, etc.
const store = createStore<RootState>({
  state: {
    user: null,
    userLoaded: false,
  },
  mutations: {
    setUser(state: RootState, user: User) {
      state.user = user;

      state.userLoaded = true;
    },
  },
});

export default store;
