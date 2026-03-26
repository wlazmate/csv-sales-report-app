<script setup>
defineProps({
    utmRanking: {
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
    <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
                {{ translations.utm }}
            </h2>

            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                {{ utmRanking.length }} {{ translations.rows }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.rank }}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.utmCampaign }}</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">{{ translations.soldTickets }}</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-if="utmRanking.length === 0">
                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                        {{ translations.noUtm }}
                    </td>
                </tr>

                <tr
                    v-for="(campaign, index) in utmRanking"
                    :key="campaign.utm_campaign"
                    class="hover:bg-gray-50"
                >
                    <td class="px-4 py-3 text-gray-700">{{ index + 1 }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ campaign.utm_campaign }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ campaign.sold_tickets }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
