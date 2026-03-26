<script setup>
import { ref, computed, watch } from 'vue';

import AppHeader from '../Components/Layout/AppHeader.vue';
import LanguageSwitcher from '../Components/Dashboard/LanguageSwitcher.vue';
import FiltersPanel from '../Components/Dashboard/FiltersPanel.vue';
import UtmRankingTable from '../Components/Dashboard/UtmRankingTable.vue';
import StatCard from '../Components/Dashboard/StatCard.vue';

const props = defineProps({
    filters: Object,
    events: Array,
    utmRanking: Array,
    filterOptions: Object,
    locale: String,
    translations: Object,
    stats: Object,
    error: String,
});

const sortKey = ref('event_date');
const sortDirection = ref('asc');

const currentPage = ref(1);
const perPage = ref(10);

const sortedEvents = computed(() => {
    return [...props.events].sort((a, b) => {
        let valA = a[sortKey.value];
        let valB = b[sortKey.value];

        if (sortKey.value === 'sold_tickets') {
            valA = Number(valA);
            valB = Number(valB);
        }

        if (valA < valB) {
            return sortDirection.value === 'asc' ? -1 : 1;
        }

        if (valA > valB) {
            return sortDirection.value === 'asc' ? 1 : -1;
        }

        return 0;
    });
});

const totalPages = computed(() => {
    return Math.max(1, Math.ceil(sortedEvents.value.length / perPage.value));
});

const paginatedEvents = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;

    return sortedEvents.value.slice(start, end);
});

const fromRow = computed(() => {
    if (sortedEvents.value.length === 0) {
        return 0;
    }

    return (currentPage.value - 1) * perPage.value + 1;
});

const toRow = computed(() => {
    return Math.min(currentPage.value * perPage.value, sortedEvents.value.length);
});

const changeSort = (key) => {
    if (sortKey.value === key) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDirection.value = 'asc';
    }

    currentPage.value = 1;
};

const goToPreviousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const goToNextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

watch(
    () => props.events,
    () => {
        currentPage.value = 1;
    }
);

watch(totalPages, (value) => {
    if (currentPage.value > value) {
        currentPage.value = value;
    }
});
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-7xl px-6 py-12">
            <AppHeader
                :title="translations.title"
                :subtitle="translations.subtitle"
            >
                <LanguageSwitcher :locale="locale" :filters="filters" />
            </AppHeader>

            <div v-if="error" class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                {{ error }}
            </div>

            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <StatCard :title="translations.totalEvents" :value="stats.total_events" />
                <StatCard :title="translations.totalTickets" :value="stats.total_tickets" />
                <StatCard :title="translations.topCity" :value="stats.top_city" />
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <FiltersPanel
                    :filters="filters"
                    :filter-options="filterOptions"
                    :translations="translations"
                />

                <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ translations.events }}
                        </h2>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                {{ sortedEvents.length }} {{ translations.rows }}
                            </span>

                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <label for="per-page">{{ translations.perPage }}</label>
                                <select
                                    id="per-page"
                                    v-model="perPage"
                                    class="rounded-lg border border-gray-300 px-2 py-1.5 text-sm"
                                >
                                    <option :value="5">5</option>
                                    <option :value="10">10</option>
                                    <option :value="20">20</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="th" @click="changeSort('event_date')">
                                    {{ translations.eventDate }}
                                </th>
                                <th class="th">
                                    {{ translations.city }}
                                </th>
                                <th class="th">
                                    {{ translations.category }}
                                </th>
                                <th class="th" @click="changeSort('sold_tickets')">
                                    {{ translations.soldTickets }}
                                </th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-if="paginatedEvents.length === 0">
                                <td colspan="4" class="py-6 text-center text-gray-500">
                                    {{ translations.noEvents }}
                                </td>
                            </tr>

                            <tr
                                v-for="event in paginatedEvents"
                                :key="`${event.event_id}-${event.event_date}-${event.city}-${event.category}`"
                                class="hover:bg-gray-50"
                            >
                                <td class="td">
                                    {{ new Date(event.event_date).toLocaleDateString(locale === 'pl' ? 'pl-PL' : 'en-GB') }}
                                </td>
                                <td class="td">{{ event.city }}</td>
                                <td class="td">{{ event.category }}</td>
                                <td class="td font-semibold">
                                    {{ Number(event.sold_tickets).toLocaleString(locale === 'pl' ? 'pl-PL' : 'en-GB') }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="sortedEvents.length > 0"
                        class="mt-4 flex flex-col gap-3 border-t border-gray-200 pt-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-sm text-gray-600">
                            {{ translations.showing }}
                            {{ fromRow }}
                            -
                            {{ toRow }}
                            {{ translations.of }}
                            {{ sortedEvents.length }}
                        </p>

                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="btn-secondary disabled:opacity-50"
                                :disabled="currentPage === 1"
                                @click="goToPreviousPage"
                            >
                                {{ translations.previous }}
                            </button>

                            <span class="px-2 text-sm text-gray-700">
                                {{ translations.page }}
                                {{ currentPage }}
                                {{ translations.of }}
                                {{ totalPages }}
                            </span>

                            <button
                                type="button"
                                class="btn-secondary disabled:opacity-50"
                                :disabled="currentPage === totalPages"
                                @click="goToNextPage"
                            >
                                {{ translations.next }}
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <div class="mt-6">
                <UtmRankingTable
                    :utm-ranking="utmRanking"
                    :translations="translations"
                />
            </div>
        </div>
    </div>
</template>
