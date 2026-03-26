<script setup>
import { reactive, watch, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    filters: Object,
    filterOptions: Object,
    translations: Object,
});

const loading = ref(false);

const form = reactive({
    city: props.filters.city ?? '',
    date_from: props.filters.date_from ?? '',
    date_to: props.filters.date_to ?? '',
    category: props.filters.category ?? '',
    lang: props.filters.lang ?? 'en',
});

watch(
    () => props.filters,
    (value) => {
        form.city = value.city ?? '';
        form.date_from = value.date_from ?? '';
        form.date_to = value.date_to ?? '';
        form.category = value.category ?? '';
        form.lang = value.lang ?? 'en';
        loading.value = false;
    },
    { deep: true }
);

const submitFilters = () => {
    loading.value = true;

    router.get('/', form, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilters = () => {
    loading.value = true;

    router.get('/', { lang: form.lang }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-1">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ translations.filters }}
        </h2>

        <form class="mt-5 space-y-4" @submit.prevent="submitFilters">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">
                    {{ translations.city }}
                </label>
                <select v-model="form.city" class="input">
                    <option value="">{{ translations.allCities }}</option>
                    <option v-for="city in filterOptions.cities" :key="city" :value="city">
                        {{ city }}
                    </option>
                </select>
            </div>

            <div>
                <label class="label">{{ translations.dateFrom }}</label>
                <input v-model="form.date_from" type="date" class="input">
            </div>

            <div>
                <label class="label">{{ translations.dateTo }}</label>
                <input v-model="form.date_to" type="date" class="input">
            </div>

            <div>
                <label class="label">{{ translations.category }}</label>
                <select v-model="form.category" class="input">
                    <option value="">{{ translations.allCategories }}</option>
                    <option value="kids">Kids</option>
                    <option value="adults">Adults</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="submit"
                    :disabled="loading"
                    class="btn-primary disabled:opacity-50"
                >
                    {{ loading ? '...' : translations.apply }}
                </button>

                <button
                    type="button"
                    :disabled="loading"
                    class="btn-secondary disabled:opacity-50"
                    @click="resetFilters"
                >
                    {{ translations.reset }}
                </button>
            </div>
        </form>
    </section>
</template>
