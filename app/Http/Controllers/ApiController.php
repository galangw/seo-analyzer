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

            return response()->json([
                'success' => true,
                'score' => $analysisResult['score'],
                'title_score' => $analysisResult['title_score'],
                'meta_score' => $analysisResult['meta_score'],
                'content_score' => $analysisResult['content_score'],
                'title_feedback' => $analysisResult['title_feedback'],
                'meta_feedback' => $analysisResult['meta_feedback'],
                'content_feedback' => $analysisResult['content_feedback'],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error
            Log::error('SEO Analysis Validation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Validation error',
                'message' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('SEO Analysis Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return a JSON error response
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while analyzing the content',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
