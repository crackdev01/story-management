<script lang="ts">
    import type { PropType } from 'vue';
    import { required } from '@vuelidate/validators'
    import useVuelidate from '@vuelidate/core';

    import ErrorMessage from './ErrorMessage.vue'

    import type { CreateForm } from '@/types';

    export default {
        components: {
            ErrorMessage,
        },
        props: {
            show: Boolean,
            error: String,
            loading: Boolean,
            onSubmit: {
                type: Function as PropType<(data: CreateForm) => void>,
                required: true,
            },
        },

        data: () => ({
            // Form Values
            title: '',
            content: '',
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
                // When loading becomes true, set error to empty
                if (newLoading) {
                    this.$emit('update:error', '');
                }
            },
            showDialog(newShowDialog) {
                // When newShowDialog becomes false, reset form
                if (!newShowDialog) {
                    // Reset form values
                    this.title = '';
                    this.content = '';
                    
                    // Reset error messages
                    this.$emit('update:error', '');

                    // Resets the $dirty state on all nested properties of a form
                    this.v$.$reset();
                }
            },
        },

        methods: {
            async handleSubmit() {
                const isFormCorrect = await this.v$.$validate()
            
                // Abort submission if the form is invalid
                if (!isFormCorrect) return

                this.onSubmit({
                    title: this.title,
                    content: this.content,
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
    <v-dialog v-model="showDialog" max-width="500px">
        <v-card>
            <v-form @submit.prevent="handleSubmit">
                <v-card-title>Create Story</v-card-title>
                <v-card-text>
                    <v-text-field 
                        label="Title*"
                        v-model="title"
                        :error-messages="v$.title.$errors.map((e: any) => e.$message)"
                        required
                    >
                    </v-text-field>
                    <v-textarea 
                        label="Content*"
                        v-model="content"
                        :error-messages="v$.content.$errors.map((e: any) => e.$message)"
                        required
                    >
                    </v-textarea>
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