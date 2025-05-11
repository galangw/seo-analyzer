<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Content;
use App\Models\SeoResult;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $recentContents = $user->contents()
            ->with('latestSeoResult')
            ->latest()
            ->take(5)
            ->get();

        // 0. Grafik konten yang diproduksi/dianalisis per hari (7 hari terakhir)
        $startDate = Carbon::now()->subDays(14)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $dailyContentStats = $this->getDailyContentStats($user->id, $startDate, $endDate);

        // 1. Total konten per user
        $totalContent = $user->contents()->count();
        $thismonthContent = $user->contents()->where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        // 2. Rata-rata overall_score yang dimiliki user
        $averageScore = SeoResult::whereHas('content', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->avg('overall_score');
        $averageScore = round($averageScore, 1) ?? 0;

        // 3. Kata kunci yang sering muncul (per kata, maks 5)
        $topKeywords = $this->getTopTargetKeywords($user->id, 5);

        return view('dashboard', compact(
            'recentContents',
            'dailyContentStats', 
            'totalContent', 
            'averageScore', 
            'topKeywords',
            'thismonthContent'
        ));
    }

    /**
     * Get daily content creation and analysis stats for chart
     */
    private function getDailyContentStats($userId, $startDate, $endDate)
    {
        // Initialize stats array with all dates
        $dates = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $dates[$dateKey] = [
                'date' => $currentDate->format('d M'),
                'content_count' => 0,
                'analyzed_count' => 0
            ];
            $currentDate->addDay();
        }

        // Get content created per day
        $createdContents = Content::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->get();

        foreach ($createdContents as $content) {
            $dateKey = $content->date;
            if (isset($dates[$dateKey])) {
                $dates[$dateKey]['content_count'] = $content->count;
            }
        }

        // Get content analyzed per day
        $analyzedContents = SeoResult::whereHas('content', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT content_id) as count')
        ->groupBy('date')
        ->get();

        foreach ($analyzedContents as $analyzed) {
            $dateKey = $analyzed->date;
            if (isset($dates[$dateKey])) {
                $dates[$dateKey]['analyzed_count'] = $analyzed->count;
            }
        }

        // Convert to indexed array for chart
        return array_values($dates);
    }

    /**
     * Get top target keywords (per word)
     */
    private function getTopTargetKeywords($userId, $limit = 5)
    {
        $contents = Content::where('user_id', $userId)->get();
        
        $wordCounts = [];
        foreach ($contents as $content) {
            // Split target keyword into words
            $words = preg_split('/\s+/', strtolower($content->target_keyword));
            foreach ($words as $word) {
                // Remove special characters and numbers
                $word = preg_replace('/[^a-z]/', '', $word);
                if (strlen($word) > 2) { // Skip very short words
                    if (!isset($wordCounts[$word])) {
                        $wordCounts[$word] = 0;
                    }
                    $wordCounts[$word]++;
                }
            }
        }
        
        // Sort by frequency
        arsort($wordCounts);
        
        // Take top N keywords
        $topKeywords = [];
        $count = 0;
        foreach ($wordCounts as $word => $frequency) {
            $topKeywords[] = [
                'word' => $word,
                'count' => $frequency
            ];
            $count++;
            if ($count >= $limit) {
                break;
            }
        }
        
        return $topKeywords;
    }
}
