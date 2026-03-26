<script setup>
defineProps({
    text: String,
});
</script>

<template>
    <div class="px-4 py-8 text-center text-gray-500">
        {{ text }}
    </div>
</template>
