<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    private $seoAnalyzerService;

    public function __construct(SeoAnalyzerService $seoAnalyzerService)
    {
        $this->seoAnalyzerService = $seoAnalyzerService;
    }

    public function analyzeContent(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'content' => 'required|string',
                'target_keyword' => 'required|string|max:100',
            ]);

            // Perform real-time analysis without saving to database
            $analysisResult = $this->seoAnalyzerService->performRealTimeAnalysis(
                $request->title,
                $request->meta_description,
                $request->content,
                $request->target_keyword
            );

            return response()->json($analysisResult, 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('SEO Analysis Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return a JSON error response
            return response()->json([
                'error' => 'An error occurred while analyzing the content',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
