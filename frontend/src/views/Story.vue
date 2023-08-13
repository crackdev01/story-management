<script lang="ts">
    import { mapState } from 'vuex';

    import httpRequest from '../helper/HttpRequest';
    import { STORY_STATUS, USER_ROLE } from "../data/Constant";
    import AppLayout from '../components/Layout.vue';
    import EditDialog from '../components/EditStory.vue';
    import ActionDialog from '../components/ActionDialog.vue';
    import AppLoader from '../components/Loading.vue';
    import StoryCard from '../components/StoryCard.vue';

    import type { HttpResponse, Story, User, EditForm } from '@/types';

    interface StoryViewData {
        story: Story | null;
        users: User[];
        loading: boolean;
        error: string;
        id: string;
        dialogEdit: boolean;
        dialogDelete: boolean;
        dialogPublish: boolean;
        loadingEdit: boolean;
        errorEdit: string;
        loadingDelete: boolean;
        errorDelete: string;
        loadingPublish: boolean;
        errorPublish: string;
    }

    export default {
        components: {
            AppLayout,
            EditDialog,
            ActionDialog,
            AppLoader,
            StoryCard
        },
        data(): StoryViewData {
            return {
                story: null,
                users: [],

                loading: true,
                error: '',

                id: "",

                dialogEdit: false,
                dialogDelete: false,
                dialogPublish: false,
                loadingEdit: false,
                errorEdit: '',
                loadingDelete: false,
                errorDelete: '',
                loadingPublish: false,
                errorPublish: '',
            };
        },

        mounted() {
            this.id = this.$route.params.id.toString();

            this.getStory();
            this.getUsers();
        },

        methods: {
            handleOpenPublishDialog() { this.dialogPublish = true; },
            handleOpenEditDialog() { this.dialogEdit = true; },
            handleOpenDeleteDialog() { this.dialogDelete = true; },

            async getUsers() {
                this.loading = true;

                httpRequest.get('/users')
                    .then((response) => {
                        this.users = response.data;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            async getStory() {
                this.loading = true;

                httpRequest.get<void, HttpResponse<Story>>('/story/' + this.id)
                    .then((response) => {
                        this.story = response.data;
                    })
                    .catch((error) => {
                        this.error = error;
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            async submitEditForm({title, content, userIds}: EditForm) {
                
                this.loadingEdit = true;

                const data = {
                    title,
                    content,
                    user_ids: userIds
                };

                httpRequest
                    .patch("story/" + this.id, data)
                    .then(() => {
                        this.getStory();

                        this.dialogEdit = false;
                    })
                    .catch((error) => {
                        this.errorEdit = error;
                    })
                    .finally(() => {
                        this.loadingEdit = false;
                    });
            },

            async submitDeleteForm() {
                this.loadingDelete = true;

                httpRequest
                    .delete("story/" + this.id)
                    .then(() => {
                        this.dialogDelete = false;

                        const dashboardUrl = "/";
                        this.$router.replace({ path: dashboardUrl })
                    })
                    .catch((error) => {
                        this.errorDelete = error;
                    })
                    .finally(() => {
                        this.loadingDelete = false;
                    });
            },

            async submitPublishForm() {
                
                this.loadingPublish = true;

                const data = {
                    status: this.storyStatusToggleValue,
                };

                httpRequest
                    .patch("story/" + this.id, data)
                    .then(() => {
                        
                        this.dialogPublish = false;

                        this.getStory();
                    })
                    .catch((error) => {
                        this.errorPublish = error;
                    })
                    .finally(() => {
                        
                        this.loadingPublish = false;
                    });
            }
        },

        computed: {
            storyStatusToggleText() {
                return this.story?.status === STORY_STATUS.DRAFT ? "Publish" : "Unpublish";
            },
            storyStatusToggleValue() {
                return this.story?.status === STORY_STATUS.DRAFT ? STORY_STATUS.PUBLISHED : STORY_STATUS.DRAFT;
            },
            editable() {
                const currentUser = this.user as User | null;

                return currentUser?.role === USER_ROLE.ADMIN || this.story?.users?.some(user => user.id === currentUser?.id);
            },
            ...mapState(['user']),
        },
    }
</script>

<template>
    <AppLayout>
        <v-container v-if="!loading" fluid>
            <v-row>
                <v-col
                    cols="12"
                >       
                    <StoryCard 
                        v-if="story && !error"
                        :story="story"
                        :isDashboardView="false"
                        :editable="editable"
                        :statusToggleText="storyStatusToggleText"
                        :onOpenPublishDialog="handleOpenPublishDialog"
                        :onOpenEditDialog="handleOpenEditDialog"
                        :onOpenDeleteDialog="handleOpenDeleteDialog"
                    />
                    
                    <v-container v-else>
                        {{ error }}
                    </v-container>
                </v-col>
            </v-row>
        </v-container>

        <!-- AppLoader -->
        <AppLoader v-else />

        <!-- Edit Dialog -->
        <EditDialog
            v-model:show="dialogEdit"
            v-model:error="errorEdit"
            :loading="loadingEdit"
            :onSubmit="submitEditForm"
            :name="story?.title"
            :story="story"
            :users="users"
        />

        <!-- Publish Dialog -->
        <ActionDialog
            v-if="story"
            v-model:show="dialogPublish"
            v-model:error="errorPublish"
            :loading="loadingPublish"
            :onSubmit="submitPublishForm"
            :name="story.title"
            :actionName="storyStatusToggleText"
            submitButtonColor="success"
        />
      
        <!-- Delete Dialog -->
        <ActionDialog
            v-if="story"
            v-model:show="dialogDelete"
            v-model:error="errorDelete"
            :loading="loadingDelete"
            :onSubmit="submitDeleteForm"
            :name="story.title"
            actionName="Delete"
            submitButtonColor="error"
        />
    </AppLayout>
</template>
