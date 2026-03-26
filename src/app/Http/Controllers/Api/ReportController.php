<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function monthly(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = $this->reportService->getMonthlyReport(auth()->id(), (int) $year, (int) $month);

        return response()->json($report);
    }

    public function yearly(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);

        $report = $this->reportService->getYearlyReport(auth()->id(), (int) $year);

        return response()->json($report);
    }

    public function appointments(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Simple monthly appointment stats
        $report = $this->reportService->getMonthlyReport(auth()->id(), (int) $year, (int) $month);

        return response()->json([
            'period' => $report['period'],
            'summary' => $report['summary'],
        ]);
    }
}
