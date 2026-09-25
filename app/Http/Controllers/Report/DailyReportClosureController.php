<?php

namespace App\Http\Controllers\Report;

use App\Application\Reports\UseCases\CloseDailyReportUseCase;
use App\Application\Reports\UseCases\GetDailyReportClosureUseCase;
use App\Domain\Reports\Exceptions\EmptyDailyReportPeriod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\CloseDailyReportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class DailyReportClosureController extends Controller
{
    public function store(
        CloseDailyReportRequest $request,
        CloseDailyReportUseCase $useCase,
    ): RedirectResponse {
        try {
            $closure = $useCase->execute(
                (int) $request->user()->getAuthIdentifier(),
                (string) $request->validated('idempotency_key'),
            );
        } catch (EmptyDailyReportPeriod $exception) {
            return redirect()
                ->route('reports.daily')
                ->with('warning', $exception->getMessage());
        }

        return redirect()->route('reports.daily-closures.show', $closure->id());
    }

    public function show(int $closure, GetDailyReportClosureUseCase $useCase): View
    {
        $dailyClosure = $useCase->execute($closure);
        abort_if($dailyClosure === null, 404);

        return view('reports.daily-closure-print', [
            'closure' => $dailyClosure,
            'report' => $dailyClosure->report(),
        ]);
    }
}
