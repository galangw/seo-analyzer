<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AiSuggestionController extends Controller
{
    protected $geminiService;
    
    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    
    public function suggestTitle(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'target_keyword' => 'required|string',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        
        // Generate title suggestion
        $suggestion = $this->geminiService->generateTitleSuggestion(
            $request->input('target_keyword'),
            $request->input('content')
        );
        
        if ($suggestion) {
            return response()->json([
                'success' => true,
                'title' => $suggestion,
            ]);
        } else {
            // Check if API key is missing or invalid
            if (empty(env('GEMINI_API_KEY'))) {
                Log::error('Gemini API Key is missing in the environment variables');
                return response()->json([
                    'success' => false,
                    'message' => 'API key configuration error. Please check the server logs.',
                    'error_code' => 'API_KEY_MISSING'
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate title suggestion. This may be due to an invalid API key or network issue.',
                'error_code' => 'API_ERROR'
            ], 500);
        }
    }

    public function suggestMetaDescription(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'target_keyword' => 'required|string',
            'content' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
            ], 422);
        }
        
        // Generate meta description suggestion
        $suggestion = $this->geminiService->generateMetaDescriptionSuggestion(
            $request->input('target_keyword'),
            $request->input('content')
        );
        
        if ($suggestion) {
            return response()->json([
                'success' => true,
                'meta_description' => $suggestion,
            ]);
        } else {
            // Check if API key is missing or invalid
            if (empty(env('GEMINI_API_KEY'))) {
                Log::error('Gemini API Key is missing in the environment variables');
                return response()->json([
                    'success' => false,
                    'message' => 'API key configuration error. Please check the server logs.',
                    'error_code' => 'API_KEY_MISSING'
                ], 500);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate meta description suggestion. This may be due to an invalid API key or network issue.',
                'error_code' => 'API_ERROR'
            ], 500);
        }
    }
} 