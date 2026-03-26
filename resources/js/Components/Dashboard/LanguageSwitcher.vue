<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    locale: {
        type: String,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const switchLanguage = (lang) => {
    router.get('/', {
        ...props.filters,
        lang,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="inline-flex rounded-lg border border-gray-300 bg-white p-1 shadow-sm">
        <button
            type="button"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition"
            :class="locale === 'pl' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
            @click="switchLanguage('pl')"
        >
            PL
        </button>

        <button
            type="button"
            class="rounded-md px-3 py-1.5 text-sm font-medium transition"
            :class="locale === 'en' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'"
            @click="switchLanguage('en')"
        >
            EN
        </button>
    </div>
</template>
