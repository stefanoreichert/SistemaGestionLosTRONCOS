<?php

namespace App\Http\Controllers;

use App\Application\Dashboard\UseCases\GetDashboardDataUseCase;
use App\Application\Table\UseCases\EnsureRestaurantTablesUseCase;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly EnsureRestaurantTablesUseCase $ensureTables,
        private readonly GetDashboardDataUseCase $dashboardData,
    ) {
    }

    public function __invoke(): View
    {
        $this->ensureTables->execute();

        return view('dashboard.index', [
            'dashboard' => $this->dashboardData->execute(),
        ]);
    }
}
