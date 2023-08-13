<script lang="ts">
    import { mapMutations } from 'vuex';
    import { email, required } from '@vuelidate/validators'
    import { useVuelidate } from '@vuelidate/core'
    
    import ErrorMessage from '../components/ErrorMessage.vue'
    import httpRequest from "../helper/HttpRequest";
    import { setCookies } from '../helper/Cookie';
    import { API_TOKEN } from '../data/Constant';

    import type { LoginData } from '@/types';

    export default {
        components: {
            ErrorMessage,
        },
        data: () => ({
            email: '',
            password: '',

            passwordFieldVisible: false,
            loading: false,
            errorLogin: '',
        }),
        setup: () => ({ 
            v$: useVuelidate() 
        }),
        validations () {
            return {
                email: { required, email },
                password: { required }
            }
        },
        watch: {
            loading(newLoading) {
                if (newLoading) {
                    this.errorLogin = '';
                }
            },
        },
        methods: {
            async submitLoginForm() {
                const isFormCorrect = await this.v$.$validate()
                
                if (!isFormCorrect) return

                this.loading = true;

                const data = {
                    email: this.email,
                    password: this.password
                };

                httpRequest
                    .post<void, LoginData>("login", data)
                    .then((response) => {
                        const { token, user } = response;
                        const dashboardUrl = "/";
                        setCookies(API_TOKEN, token);
                        this.setUser(user);

                        this.$router.replace({ path: dashboardUrl })
                    })
                    .catch((error) => {
                        alert(error);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            ...mapMutations(['setUser']),
        },
    }
</script>
<template>
    <div class="d-flex align-center h-full">  
      <v-card
        class="mx-auto pa-12 pb-8"
        elevation="8"
        max-width="448"
        min-width="448"
        rounded="lg"
      >
        <v-form @submit.prevent="submitLoginForm">
            <div class="text-subtitle-1 text-medium-emphasis ">Email Address</div>
    
            <v-text-field
                v-model="email"
                :error-messages="v$.email.$errors.map((e: any) => 'Email address is required')"
                density="compact"
                placeholder="Email address"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
                required
            ></v-text-field>
    
            <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
            Password
            </div>
    
            <v-text-field
                v-model="password"
                :error-messages="v$.password.$errors.map((e: any) => 'Password is required')"
                :type="passwordFieldVisible ? 'text' : 'password'"
                density="compact"
                placeholder="Password"
                prepend-inner-icon="mdi-lock-outline"
                variant="outlined"
                @click:append-inner="passwordFieldVisible = !passwordFieldVisible"
            ></v-text-field>
    
            <v-card
            class="mb-5"
            color="surface-variant"
            variant=""
            >            
            </v-card>
    
            <v-btn
                block
                type="submit"
                class="mb-8"
                color="black"
                size="large"
                outlined
                :loading="loading"
            >
            Log In
            </v-btn>

            <!-- <ErrorMessage :message="errorLogin" /> -->

        </v-form>
      
      </v-card>
    </div>
</template>


<style scoped>
.h-full {
    height: 100%;
}
</style>