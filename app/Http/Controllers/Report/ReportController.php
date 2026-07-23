<?php

namespace App\Http\Controllers\Report;

use App\Application\Reports\DTOs\DailySalesReportQuery;
use App\Application\Reports\DTOs\SoldProductsReportQuery;
use App\Application\Reports\UseCases\GetDailyReportUseCase;
use App\Application\Reports\UseCases\GetDailySalesReportUseCase;
use App\Application\Reports\UseCases\GetMonthlyReportUseCase;
use App\Application\Reports\UseCases\GetSoldProductsReportUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\DailyReportRequest;
use App\Http\Requests\Report\MonthlyReportRequest;
use App\Http\Requests\Report\SoldProductsReportRequest;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function daily(DailyReportRequest $request, GetDailyReportUseCase $useCase): View
    {
        $date = (string) ($request->validated('date') ?? now()->toDateString());

        return view('reports.daily', [
            'report' => $useCase->execute($date),
        ]);
    }

    public function monthly(MonthlyReportRequest $request, GetMonthlyReportUseCase $useCase): View
    {
        $month = (int) ($request->validated('month') ?? now()->month);
        $year = (int) ($request->validated('year') ?? now()->year);

        return view('reports.monthly', [
            'report' => $useCase->execute($month, $year),
        ]);
    }

    public function soldProducts(SoldProductsReportRequest $request, GetSoldProductsReportUseCase $useCase): View
    {
        $validated = $request->validated();
        $period = (string) ($validated['period'] ?? 'today');

        [$from, $to] = match ($period) {
            'month' => [now()->startOfMonth()->toDateString(), now()->toDateString()],
            'custom' => [(string) $validated['from'], (string) $validated['to']],
            default => [now()->toDateString(), now()->toDateString()],
        };

        return view('reports.sold-products', [
            'period' => $period,
            'report' => $useCase->execute(new SoldProductsReportQuery($from, $to)),
        ]);
    }

    public function dailySales(GetDailySalesReportUseCase $useCase): View
    {
        return view('reports.daily-sales', [
            'report' => $useCase->execute(new DailySalesReportQuery(now()->toDateString())),
        ]);
    }
}
