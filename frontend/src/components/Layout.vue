<script lang="ts">
    import { mapMutations, mapState } from 'vuex';
    
    import { API_TOKEN } from '../data/Constant';
    import { removeCookies } from '../helper/Cookie';


    export default {
        components: {
         
        },
        methods: {
            async handleLogout(){
                removeCookies(API_TOKEN);

                this.setUser(null);
                
                this.$router.replace({ path: '/login' })
            },

            ...mapMutations(['setUser']),
        },

        computed: {
            avatarUser() {
                return {
                    name: this.user?.name,
                    email: this.user?.email,
                }
            },
            ...mapState(['user']),
        },
    };
</script>

<template>
    <v-app-bar class="header">
        <v-container>
            <v-row class="justify-space-between d-flex">                
                <router-link to="/">
                    <v-btn color="white" variant="text">Story Management</v-btn>
                </router-link>
                <v-btn color="black logout-btn" @click="handleLogout">Logout</v-btn>
            </v-row>
        </v-container>
    </v-app-bar>

    <v-main>
        <slot></slot>
    </v-main>

    <v-footer app>
      <!-- -->
    </v-footer>
</template>

<style scoped>
.header {
    background: rgb(30 41 59);
}

.logout-btn {
    background-color: white;
}
</style>