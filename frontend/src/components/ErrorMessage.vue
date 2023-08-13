<script setup lang="ts">
    defineProps<{
        message: string
    }>()
</script>

<template>
    <div class="error-message">
        {{ message }}
    </div> 
</template>

<style scoped>
    .error-message {
        display: block;
        color: #f44336;
        font-size: 0.75rem;
    }
</style>
