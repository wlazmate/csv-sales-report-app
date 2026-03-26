<?php

declare(strict_types=1);

namespace App\Services\Sales;

use Illuminate\Support\Facades\App;

final class DashboardPageDataBuilder
{
    public function __construct(
        private readonly SalesReportService $salesReportService,
    ) {
    }

    public function build(array $filters): array
    {
        $lang = $filters['lang'] ?? 'en';
        App::setLocale($lang);

        try {
            $report = $this->salesReportService->buildReport(
                storage_path('app/data/sales.csv'),
                $filters
            );
        } catch (\Throwable $e) {
            return [
                'filters' => $filters,
                'events' => [],
                'utmRanking' => [],
                'filterOptions' => [
                    'cities' => [],
                    'categories' => ['kids', 'adults'],
                ],
                'stats' => [
                    'total_events' => 0,
                    'total_tickets' => 0,
                    'top_city' => null,
                ],
                'locale' => $lang,
                'translations' => $this->translations(),
                'error' => __('dashboard.csv_error'),
            ];
        }

        return [
            'filters' => $filters,
            'events' => $report['events'],
            'utmRanking' => $report['utm_ranking'],
            'filterOptions' => $report['filter_options'],
            'stats' => $report['stats'],
            'locale' => $lang,
            'translations' => $this->translations(),
            'error' => null,
        ];
    }

    private function translations(): array
    {
        return [
            'title' => __('dashboard.title'),
            'subtitle' => __('dashboard.subtitle'),
            'filters' => __('dashboard.filters'),
            'events' => __('dashboard.events'),
            'utm' => __('dashboard.utm'),
            'apply' => __('dashboard.apply'),
            'reset' => __('dashboard.reset'),
            'allCities' => __('dashboard.all_cities'),
            'allCategories' => __('dashboard.all_categories'),
            'noEvents' => __('dashboard.no_events'),
            'noUtm' => __('dashboard.no_utm'),
            'city' => __('dashboard.city'),
            'dateFrom' => __('dashboard.date_from'),
            'dateTo' => __('dashboard.date_to'),
            'category' => __('dashboard.category'),
            'eventDate' => __('dashboard.event_date'),
            'soldTickets' => __('dashboard.sold_tickets'),
            'rank' => __('dashboard.rank'),
            'utmCampaign' => __('dashboard.utm_campaign'),
            'rows' => __('dashboard.rows'),
            'totalEvents' => __('dashboard.total_events'),
            'totalTickets' => __('dashboard.total_tickets'),
            'topCity' => __('dashboard.top_city'),
            'csvError' => __('dashboard.csv_error'),
            'previous' => __('dashboard.previous'),
            'next' => __('dashboard.next'),
            'page' => __('dashboard.page'),
            'of' => __('dashboard.of'),
            'showing' => __('dashboard.showing'),
            'perPage' => __('dashboard.per_page'),
        ];
    }
}
