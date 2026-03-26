<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DashboardFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city' => ['nullable', 'string', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'category' => ['nullable', 'in:kids,adults'],
            'lang' => ['nullable', 'in:pl,en'],
        ];
    }

    /**
     * @return array{
     *   city: ?string,
     *   date_from: ?string,
     *   date_to: ?string,
     *   category: ?string,
     *   lang: string
     * }
     */
    public function filters(): array
    {
        return [
            'city' => $this->string('city')->toString() ?: null,
            'date_from' => $this->string('date_from')->toString() ?: null,
            'date_to' => $this->string('date_to')->toString() ?: null,
            'category' => $this->string('category')->toString() ?: null,
            'lang' => $this->string('lang')->toString() ?: 'en',
        ];
    }
}
