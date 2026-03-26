<?php

declare(strict_types=1);

namespace App\Services\Sales;

use App\Data\Sales\SaleRecord;
use Illuminate\Support\Collection;
use RuntimeException;

final class CsvSalesReader
{
    /**
     * @return Collection<int, SaleRecord>
     */
    public function read(string $path): Collection
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException(sprintf('CSV file not found or not readable: %s', $path));
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException(sprintf('Unable to open CSV file: %s', $path));
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);

            return collect();
        }

        $header = array_map(
            static fn (mixed $value): string => trim((string) $value),
            $header
        );

        $records = collect();

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $row = array_pad($row, count($header), null);
            $data = array_combine($header, $row);

            if ($data === false) {
                continue;
            }

            $records->push($this->mapRowToRecord($data));
        }

        fclose($handle);

        return $records;
    }

    /**
     * @param array<int, mixed> $row
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function mapRowToRecord(array $data): SaleRecord
    {
        return new SaleRecord(
            eventId: trim((string) ($data['event_id'] ?? '')),
            eventDate: trim((string) ($data['event_date'] ?? '')),
            city: trim((string) ($data['city'] ?? '')),
            category: trim((string) ($data['category'] ?? '')),
            orderId: trim((string) ($data['order_id'] ?? '')),
            ticketQty: (int) ($data['ticket_qty'] ?? 0),
            status: trim((string) ($data['status'] ?? '')),
            utmSource: $this->nullableString($data['utm_source'] ?? null),
            utmCampaign: $this->nullableString($data['utm_campaign'] ?? null),
            utmContent: $this->nullableString($data['utm_content'] ?? null),
            soldOut: $this->toBool($data['sold_out'] ?? false),
        );
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function toBool(mixed $value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes'], true);
    }
}
