<template>
    <div>
        <h1>Plan Rate</h1>
        <p>List of Plan Rates</p>
    </div>
</template>
<script setup lang="ts">
import { apiService } from '../services';

// Example usage
const getData = async () => {
    try {
        const response = await apiService.get('/plannerate');
        // Handle response
    } catch (error) {
        // Handle error
    }
};
</script>
