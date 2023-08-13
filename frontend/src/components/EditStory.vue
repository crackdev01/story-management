<script lang="ts">
    import type { PropType } from 'vue';
    import { required } from '@vuelidate/validators'
    import useVuelidate from '@vuelidate/core';

    import ErrorMessage from './ErrorMessage.vue'

    import type { EditForm, Story, StoryUser } from '@/types';
    

    export default {
        components: {
            ErrorMessage,
        },
        props: {
            show: Boolean,
            error: String,
            loading: Boolean,
            onSubmit: {
                type: Function as PropType<(data: EditForm) => void>,
                required: true,
            },
            name: String,
            story: {
                type: (Object as PropType<Story | null>),
            },
            users: Array,
        },

        data: () => ({
            // Form Values
            title: '',
            content: '',
            status: '',
            userIds: [] as number[],
        }),

        setup: () => ({ 
            v$: useVuelidate() 
        }),
        validations () {
            return {
                title: { required },
                content: { required }
            }
        },

        watch: {
            loading(newLoading) {

                if (newLoading) {
                    this.$emit('update:error', '');
                }
            },
            showDialog(newShowDialog) {
                
                if (!newShowDialog) {
                    
                    this.title = this.story?.title || "";
                    this.content = this.story?.content || "";
                    this.userIds = this.story?.users?.map((user: StoryUser) => user.id) || [];
                        
                    
                    this.$emit('update:error', '');

                    
                    this.v$.$reset();
                }
            },
            story: {
                handler(newStory) {
                    
                    this.title = newStory.title;
                    this.content = newStory.content;
                    this.status = newStory.status;
                    this.userIds = newStory.users.map((user: StoryUser) => user.id);
                },
                deep: true,
            },
        },

        methods: {
            async handleSubmit() {
                const isFormCorrect = await this.v$.$validate()
            
                
                if (!isFormCorrect) return

                this.onSubmit({
                    title: this.title,
                    content: this.content,
                    userIds: this.userIds,
                });
            },
        },

        computed: {
            showDialog: {
                get() {
                    return this.show;
                },
                set(val: boolean) {
                    this.$emit('update:show', val);
                },
            },
            errorDialog: {
                get() {
                    return this.error || "";
                },
                set(val: string) {
                    this.$emit('update:error', val);
                },
            },
        },
    };
</script>
<template>
    <v-dialog v-model="showDialog" width="1024">
        <v-card>
            <v-form @submit.prevent="handleSubmit">
                <v-card-title>Edit {{ name }}</v-card-title>
                <v-card-text>
                    <v-text-field v-model="title" label="Title*" :error-messages="v$.title.$errors.map((e: any) => 'Title is required')" required></v-text-field>
                    <v-textarea v-model="content" label="Content*" :error-messages="v$.content.$errors.map((e: any) => 'Content is required')"></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <ErrorMessage :message="errorDialog" />

                    <v-spacer></v-spacer>
                    
                    <v-btn @click="$emit('update:show', false)">Cancel</v-btn>
                    <v-btn type="submit" :loading="loading" color="primary">Save</v-btn>
                </v-card-actions>

            </v-form>
        </v-card>
    </v-dialog>
</template>