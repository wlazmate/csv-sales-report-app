<script setup>
defineProps({
    title: String,
    value: [String, Number],
});
</script>

<template>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">{{ title }}</p>
        <p class="mt-2 text-2xl font-semibold text-gray-900">
            {{ value ?? '-' }}
        </p>
    </div>
</template>
