<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    public function getChartsData(): array
    {
        $allTypes = DB::table('templates')
            ->pluck('name')
            ->toArray();

        return [
            'weekly' => [
                'downloads' => $this->getWeeklyDownloads(),
                'cv_types' => $this->getWeeklyCvTypes($allTypes),
            ],
            'monthly' => [
                'downloads' => $this->getMonthlyDownloads(),
                'cv_types' => $this->getMonthlyCvTypes($allTypes),
            ],
        ];
    }

    public function getAllTimeInsights(): array
    {

        return [
            'absolute_records' => $this->getAbsoluteRecords(),
            'best_days_of_week' => $this->getBestDaysOfWeek(),
            'type_color_trends' => $this->getTypeColorTrends(),
        ];
    }

    private function getWeeklyDownloads(): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        $downloads = DB::table('cvs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateString = Carbon::now()->subDays($i)->toDateString();
            $chartData[$dateString] = $downloads[$dateString] ?? 0;
        }

        return $chartData;
    }

    private function getMonthlyDownloads(): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        $downloads = DB::table('cvs')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->pluck('count', 'month');

        $chartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthString = Carbon::now()->subMonths($i)->format('Y-m');
            $chartData[$monthString] = $downloads[$monthString] ?? 0;
        }

        return $chartData;
    }

    private function getAbsoluteRecords()
    {
        return DB::table('cvs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();
    }

    private function getBestDaysOfWeek(): array
    {
        $weeklyDistribution = DB::table('cvs')
            ->select(DB::raw('DAYOFWEEK(created_at) as day_num'), DB::raw('count(*) as count'))
            ->groupBy('day_num')
            ->pluck('count', 'day_num');

        $daysMap = [
            1 => 'Vasárnap', 2 => 'Hétfő', 3 => 'Kedd',
            4 => 'Szerda', 5 => 'Csütörtök', 6 => 'Péntek', 7 => 'Szombat',
        ];

        $bestDays = [];
        foreach ($daysMap as $num => $name) {
            $bestDays[$name] = $weeklyDistribution[$num] ?? 0;
        }

        return $bestDays;
    }

    private function getWeeklyCvTypes(array $allTypes): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        // Lekérjük az elmúlt 7 nap összesítését típusonként
        $raw = DB::table('cvs')
            ->select('cv_type', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('cv_type')
            ->groupBy('cv_type')
            ->pluck('count', 'cv_type'); // Kimenet pl.: ['Berlin' => 15, 'London' => 3]

        // Biztosítjuk, hogy minden létező típus szerepeljen a listában, ha nincs adat, 0-val
        $result = [];
        foreach ($allTypes as $type) {
            $result[$type] = $raw[$type] ?? 0;
        }

        return $result; // Eredmény: ['Berlin' => 15, 'Stockholm' => 0, 'London' => 3]
    }

    private function getMonthlyCvTypes(array $allTypes): array
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();

        // Lekérjük az elmúlt 12 hónap összesítését típusonként
        $raw = DB::table('cvs')
            ->select('cv_type', DB::raw('count(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('cv_type')
            ->groupBy('cv_type')
            ->pluck('count', 'cv_type');

        // Biztosítjuk, hogy a havi listában is minden típus ott legyen, akár 0-val is
        $result = [];
        foreach ($allTypes as $type) {
            $result[$type] = $raw[$type] ?? 0;
        }

        return $result;
    }

    private function getTypeColorTrends(): array
    {
        // 1. Lekérjük az összes sablont az ID-jával és a nevével együtt
        $templates = DB::table('templates')
            ->select('id', 'name')
            ->get(); // Ebből tudjuk pl., hogy id: 1 az 'Berlin'

        // 2. Lekérjük az összes hivatalosan választható színt a sablonokhoz
        $allowedColors = DB::table('colors')
            ->select('template_id', 'color')
            ->get()
            ->groupBy('template_id'); // Csoportosítjuk template_id szerint a könnyű kereséshez

        // 3. Lekérjük a valós használati statisztikát a felhasználóktól (cvs tábla)
        $usageStats = DB::table('cvs')
            ->select('cv_type', 'picked_bg', DB::raw('count(*) as count'))
            ->whereNotNull('cv_type')
            ->whereNotNull('picked_bg')
            ->groupBy('cv_type', 'picked_bg')
            ->get()
            // Kulcs-érték párrá alakítjuk: 'Berlin_#2e294e' => 18
            ->keyBy(function ($item) {
                return $item->cv_type.'_'.$item->picked_bg;
            });

        $result = [];

        // 4. Összefésüljük az adatokat, hogy a 0-ás színek is meglegyenek
        foreach ($templates as $template) {
            $result[$template->name] = [];

            // Megnézzük, hogy ehhez a sablonhoz vannak-e beállítva színek a colors táblában
            if (isset($allowedColors[$template->id])) {
                foreach ($allowedColors[$template->id] as $colorObj) {
                    $colorHex = $colorObj->color;

                    // Megnézzük, hányszor használták ezt a konkrét kombinációt (pl. 'Berlin_#2e294e')
                    $key = $template->name.'_'.$colorHex;
                    $count = isset($usageStats[$key]) ? $usageStats[$key]->count : 0;

                    $result[$template->name][] = [
                        'color' => $colorHex,
                        'count' => $count,
                    ];
                }
            }

            // Biztonsági rendezés: típuson belül a legnépszerűbb szín legyen legfelül a listában
            usort($result[$template->name], function ($a, $b) {
                return $b['count'] <=> $a['count'];
            });
        }

        return $result;
    }

    private function getFeedbackAverages(): array
    {
        // Időpontok meghatározása a Carbon segítségével
        $todayStart = Carbon::today();
        $weeklyStart = Carbon::now()->subDays(6)->startOfDay();
        $monthlyStart = Carbon::now()->subMonths(11)->startOfMonth(); // 12 hónapos gördülő ablak

        // Lekérjük az összes szükséges átlagot egy menetben
        $todayAvg = DB::table('feedback')
            ->where('created_at', '>=', $todayStart)
            ->avg('rating');

        $weeklyAvg = DB::table('feedback')
            ->where('created_at', '>=', $weeklyStart)
            ->avg('rating');

        $monthlyAvg = DB::table('feedback')
            ->where('created_at', '>=', $monthlyStart)
            ->avg('rating');

        $alltimeAvg = DB::table('feedback')
            ->avg('rating');

        // Visszaadjuk az értékeket formázva (ha nincs adat, akkor 0-át adunk vissza null helyett)
        return [
            'today' => $todayAvg ? round($todayAvg, 1) : 0,
            'weekly' => $weeklyAvg ? round($weeklyAvg, 1) : 0,
            'monthly' => $monthlyAvg ? round($monthlyAvg, 1) : 0,
            'alltime' => $alltimeAvg ? round($alltimeAvg, 1) : 0,
        ];
    }

    public function getFeedbackData(): array
    {
        return [
            'averages' => $this->getFeedbackAverages(),
            'all_messages' => $this->getAllMessages(),
        ];
    }

    private function getAllMessages()
    {

        return DB::table('feedback')
            ->select('id', 'rating', 'message', 'created_at')
            ->whereNotNull('message')
            ->where('message', '!=', '')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
}
