<script lang="ts">
    import type { PropType } from 'vue';

    import truncateString from '../helper/Formatter';
    
    import type { Story } from '@/types';

    export default {
        props: {
            story: {
                type: Object as PropType<Story>,
                required: true,
            },
            isDashboardView: Boolean,
            editable: Boolean,
            statusToggleText: String,
            onOpenPublishDialog: {
                type: Function as PropType<() => void>,
            },
            onOpenEditDialog: {
                type: Function as PropType<() => void>,
            },
            onOpenDeleteDialog: {
                type: Function as PropType<() => void>,
            },
        },

        methods: {
            handleOpenPublishDialog() {
                this.onOpenPublishDialog && this.onOpenPublishDialog();
            },
            handleOpenEditDialog() {
                this.onOpenEditDialog && this.onOpenEditDialog();
            },
            handleOpenDeleteDialog() {
                this.onOpenDeleteDialog && this.onOpenDeleteDialog();
            },
        },

        computed: {
            truncateString() {
                return truncateString;
            },
        },
    };
</script>
<template>
    <v-card class="h-100">
        <v-card-title>{{ story.title }}</v-card-title>
        <v-card-subtitle>Owner: {{ story.users.map(user => user.name).join(',') }}</v-card-subtitle>
        <v-card-text>
            {{ isDashboardView && story.content ? truncateString(story.content) : story.content }}
        </v-card-text>
        <v-card-actions v-if="isDashboardView || !editable">
            <v-card-text>{{ story.status }}</v-card-text>
        </v-card-actions>
        <v-card-actions v-else>
            <v-card-text>{{ story.status }}</v-card-text>
            <v-btn color="success" @click="handleOpenPublishDialog">{{ statusToggleText }}</v-btn>
            <v-btn color="primary" @click="handleOpenEditDialog">Edit</v-btn>
            <v-btn color="error" @click="handleOpenDeleteDialog">Delete</v-btn>
        </v-card-actions>
    </v-card>
</template>