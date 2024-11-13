<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('role:admin');
        $this->dashboardService = $dashboardService;
    }

    public function overview(): JsonResponse
    {
        $stats = $this->dashboardService->getAdminOverview();

        return response()->json([
            'stats' => $stats
        ]);
    }
} 