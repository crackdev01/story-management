<script lang="ts">
    import httpRequest from '../helper/HttpRequest';
    import truncateString from '../helper/Formatter';
    import AppLayout from '../components/Layout.vue';
    import CreateDialog from '../components/NewStory.vue';
    import AppLoader from '../components/Loading.vue';
    import StoryCard from '../components/StoryCard.vue';

    import type { Story, HttpResponseList, CreateForm } from '@/types';

    export default {
        components: {
            AppLayout,
            AppLoader,
            CreateDialog,
            StoryCard,
        },
        data: () => ({
            stories: [] as Story[],
               
            dialogCreate: false,
            loadingCreate: false,
            errorCreate: '',

            loading: true,
        }),

        mounted() {            
            this.getStories();
        },

        methods: {            
            async getStories() {
                
                this.loading = true;

                httpRequest.get<void, HttpResponseList<Story>>('/stories')
                    .then((response) => {
                        this.stories = response.data;
                    })
                    .finally(() => {
                        
                        this.loading = false;                        
                    });
            },

            async submitCreateForm({title, content}: CreateForm) {
                
                this.loadingCreate = true;

                const data = {
                    title,
                    content
                };

                
                httpRequest
                    .post("story", data)
                    .then(() => {
                        
                        this.dialogCreate = false;
                       
                        this.getStories();
                    })
                    .catch((error) => {
                        
                        this.errorCreate = error;
                    })
                    .finally(() => {                      
                        this.loadingCreate = false;
                    });
            },

            
        },

        computed: {
            truncateString() {
                return truncateString;
            },
          
        },
    }

</script>

<template>
    <AppLayout>
        <v-container v-if="!loading">
            <v-row>
                <v-col cols="12">
                    <v-btn color="white" @click="dialogCreate = true">Create Story</v-btn>
                </v-col>
            </v-row>
             <v-table class="mt-3 color-white">
                <thead>
                  <tr>
                    <th class="text-left">
                      Title
                    </th>
                    <th class="text-left">
                      Content
                    </th>
                    <th class="text-left">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="item in stories"
                    :key="item.name"
                  >
                    <td>
                        
                        <router-link :to="`/story/${item.id}`">
                        {{ item.title }}
                        </router-link>    
                    </td>                   
                    <td>{{ item.content }}</td>
                    <td>{{ item.status }}</td>
                  
                  </tr>
                </tbody>
            </v-table>
        </v-container>

        <!-- AppLoader -->
        <AppLoader v-else />

        <!-- Create Dialog -->
        <CreateDialog
            v-model:show="dialogCreate"
            v-model:error="errorCreate"
            :loading="loadingCreate"
            :onSubmit="submitCreateForm"
        />

    </AppLayout>
</template>
