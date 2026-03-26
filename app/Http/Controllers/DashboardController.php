<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DashboardFiltersRequest;
use App\Services\Sales\DashboardPageDataBuilder;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __invoke(
        DashboardFiltersRequest $request,
        DashboardPageDataBuilder $builder
    ): Response {
        $data = $builder->build($request->filters());

        return Inertia::render('Dashboard', $data);
    }
}
