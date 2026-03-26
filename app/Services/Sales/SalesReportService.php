<?php

declare(strict_types=1);

namespace App\Services\Sales;

use App\Data\Sales\SaleRecord;
use Illuminate\Support\Collection;

final class SalesReportService
{
    public function __construct(
        private readonly CsvSalesReader $csvSalesReader,
    ) {
    }

    /**
     * @param array{
     *     city?: string|null,
     *     date_from?: string|null,
     *     date_to?: string|null,
     *     category?: string|null
     * } $filters
     * @return array{
     *     events: array<int, array<string, mixed>>,
     *     utm_ranking: array<int, array<string, mixed>>,
     *     filter_options: array<string, mixed>,
     *     stats: array<string, mixed>
     * }
     */
    public function buildReport(string $csvPath, array $filters = []): array
    {
        $records = $this->csvSalesReader->read($csvPath);

        $filteredRecords = $this->applyFilters($records, $filters);

        return [
            'events' => $this->buildEventsList($filteredRecords),
            'utm_ranking' => $this->buildUtmRanking($filteredRecords),
            'filter_options' => [
                'cities' => $records
                    ->pluck('city')
                    ->filter()
                    ->unique()
                    ->sort()
                    ->values()
                    ->all(),
                'categories' => ['kids', 'adults'],
            ],
            'stats' => $this->buildStats($filteredRecords),
        ];
    }

    /**
     * @param Collection<int, SaleRecord> $records
     * @param array<string, mixed> $filters
     * @return Collection<int, SaleRecord>
     */
    private function applyFilters(Collection $records, array $filters): Collection
    {
        return $records
            ->when(
                filled($filters['city'] ?? null),
                fn (Collection $collection): Collection => $collection->filter(
                    fn (SaleRecord $record): bool => $record->city === $filters['city']
                )
            )
            ->when(
                filled($filters['date_from'] ?? null),
                fn (Collection $collection): Collection => $collection->filter(
                    fn (SaleRecord $record): bool => $record->eventDate >= $filters['date_from']
                )
            )
            ->when(
                filled($filters['date_to'] ?? null),
                fn (Collection $collection): Collection => $collection->filter(
                    fn (SaleRecord $record): bool => $record->eventDate <= $filters['date_to']
                )
            )
            ->when(
                filled($filters['category'] ?? null),
                fn (Collection $collection): Collection => $collection->filter(
                    fn (SaleRecord $record): bool => $record->category === $filters['category']
                )
            )
            ->values();
    }

    /**
     * @param Collection<int, SaleRecord> $records
     * @return array<int, array<string, mixed>>
     */
    private function buildEventsList(Collection $records): array
    {
        return $records
            ->filter(fn (SaleRecord $record): bool => $record->isConfirmed())
            ->groupBy(fn (SaleRecord $record): string => implode('|', [
                $record->eventId,
                $record->eventDate,
                $record->city,
                $record->category,
            ]))
            ->map(function (Collection $group): array {
                /** @var SaleRecord $first */
                $first = $group->first();

                return [
                    'event_id' => $first->eventId,
                    'event_date' => $first->eventDate,
                    'city' => $first->city,
                    'category' => $first->category,
                    'sold_tickets' => $group->sum(fn (SaleRecord $record): int => $record->ticketQty),
                ];
            })
            ->sortBy([
                ['event_date', 'asc'],
                ['city', 'asc'],
                ['category', 'asc'],
            ])
            ->values()
            ->all();
    }

    /**
     * @param Collection<int, SaleRecord> $records
     * @return array<int, array<string, mixed>>
     */
    private function buildUtmRanking(Collection $records): array
    {
        return $records
            ->filter(fn (SaleRecord $record): bool => $record->isConfirmed())
            ->filter(fn (SaleRecord $record): bool => filled($record->utmCampaign))
            ->groupBy(fn (SaleRecord $record): string => (string) $record->utmCampaign)
            ->map(function (Collection $group, string $utmCampaign): array {
                return [
                    'utm_campaign' => $utmCampaign,
                    'sold_tickets' => $group->sum(fn (SaleRecord $record): int => $record->ticketQty),
                ];
            })
            ->sortByDesc('sold_tickets')
            ->take(10)
            ->values()
            ->all();
    }

    /**
     * @param Collection<int, SaleRecord> $records
     */
    private function buildStats(Collection $records): array
    {
        $confirmed = $records->filter(fn (SaleRecord $r): bool => $r->isConfirmed());

        return [
            'total_events' => $confirmed
                ->groupBy(fn (SaleRecord $r): string => $r->eventId)
                ->count(),

            'total_tickets' => $confirmed->sum(fn (SaleRecord $r): int => $r->ticketQty),

            'top_city' => $confirmed
                ->groupBy(fn (SaleRecord $r): string => $r->city)
                ->map(fn (Collection $group): int => $group->sum(fn (SaleRecord $r): int => $r->ticketQty))
                ->sortDesc()
                ->keys()
                ->first(),
        ];
    }
}
