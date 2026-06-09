<?php

namespace App\Http\Controllers;

use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    //
    protected $analytics;

    public function __construct(DashboardAnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function getAdminPageInfo(Request $request)
    {
        return response()->json([
            'charts' => $this->analytics->getChartsData(),
            'all_time_insights' => $this->analytics->getAllTimeInsights(),
            'feedback_analytics' => $this->analytics->getFeedbackData(),
        ]);
    }
}
