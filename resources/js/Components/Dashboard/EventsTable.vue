<script setup>
import EmptyState from '../Layout/EmptyState.vue';
defineProps({
    events: {
        type: Array,
        required: true,
    },
    translations: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
                {{ translations.events }}
            </h2>

            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                {{ events.length }} {{ translations.rows }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.eventDate }}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.city }}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.category }}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.soldTickets }}</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-if="events.length === 0">
                    <td colspan="4">
                        <EmptyState :text="translations.noEvents" />
                    </td>
                </tr>

                <tr
                    v-for="event in events"
                    :key="`${event.event_id}-${event.event_date}-${event.city}-${event.category}`"
                    class="hover:bg-gray-50"
                >
                    <td class="px-4 py-3 text-gray-700">{{ event.event_date }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ event.city }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ event.category }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ event.sold_tickets }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
