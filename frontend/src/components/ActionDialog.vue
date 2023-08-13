<script lang="ts">
    import type { PropType } from 'vue';
    
    import ErrorMessage from './ErrorMessage.vue'

    export default {
        components: {
            ErrorMessage,
        },
        props: {
            show: Boolean,
            error: String,
            loading: Boolean,
            onSubmit: {
                type: Function as PropType<() => void>,
                required: true,
            },
            name: String,
            actionName: String,
            submitButtonColor: String,
        },

        watch: {
            loading(newLoading) {
                // When loading becomes true, set error to empty
                if (newLoading) {
                    this.$emit('update:error', '');
                }
            },
            showDialog(newShowDialog) {
                if (!newShowDialog) {
                    // Reset error messages
                    this.$emit('update:error', '');

                }
            },
        },

        methods: {
            async handleSubmit() {
                this.onSubmit();
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
                <v-card-title>{{ actionName }} {{ name }}</v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                        <v-col
                            cols="12"
                        >
                            <v-card-text>
                                Are you sure you want to {{ actionName }} {{ name }}?
                            </v-card-text>
                        </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <ErrorMessage :message="errorDialog" />

                    <v-spacer></v-spacer>
                    
                    <v-btn @click="$emit('update:show', false)">Cancel</v-btn>
                    <v-btn type="submit" :loading="loading" :color="submitButtonColor">{{ actionName }}</v-btn>
                </v-card-actions>

            </v-form>
        </v-card>
    </v-dialog>
</template>