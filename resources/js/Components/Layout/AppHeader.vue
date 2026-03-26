<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <header class="mb-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                {{ title }}
            </h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-600">
                {{ subtitle }}
            </p>
        </div>

        <slot />
    </header>
</template>
