<script lang="ts">
    import type { PropType } from 'vue';
    export default {
        props: {
            user: {
                type: Object,
                required: true,
                default: () => ({ name: '', email: '' }),
            },
            onClickLogout: {
                type: Function as PropType<() => void>,
                required: true,
            },
        },
        name: 'UserAvatar',
        computed: {
        initials() {
            if(this.user.name){
                return this.user.name.split(' ').map((part: string) => part.charAt(0)).join('').toUpperCase();
            }
            
            return '';
        },
        },
    }
</script>


<template>
    <v-menu
        min-width="200px"
        rounded
    >
        <template v-slot:activator="{ props }">
        <v-btn
            icon
            v-bind="props"
            class="float-end"
            @click="onClickLogout"
        >
            <v-card-text>
                Logout
            </v-card-text>
        </v-btn>
        </template>
    </v-menu>
  </template>