<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\SeoAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    private $seoAnalyzerService;

    public function __construct(SeoAnalyzerService $seoAnalyzerService)
    {
        $this->middleware('auth');
        $this->seoAnalyzerService = $seoAnalyzerService;
    }

    public function index(Request $request)
    {
        $query = Auth::user()->contents()
            ->with('latestSeoResult');
            
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('target_keyword', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        $contents = $query->latest()->paginate(10);

        return view('contents.index', compact('contents'));
    }

    public function create()
    {
        return view('contents.create');
    }

    public function store(Request $request)
    {
        try {
            // Generate unique request ID for tracking duplicate requests
            $requestId = $request->input('request_id', uniqid('req_'));
            
            // Use the Cache to implement a simple lock mechanism to prevent duplicate submissions
            $lockKey = 'content_submission_lock_' . $requestId;
            
            // Check if this request is already being processed
            if (Cache::has($lockKey)) {
                Log::warning("Duplicate request detected. Aborting. [{$requestId}]");
                
                return response()->json([
                    'success' => false,
                    'message' => 'Your request is already being processed. Please wait.'
                ], 429); // Too Many Requests
            }
            
            // Set a lock for this request (expires after 30 seconds)
            Cache::put($lockKey, true, 30);
            
            // Log full request data for debugging
            Log::info("Content store request started [{$requestId}]:", [
                'title_length' => strlen($request->input('title', '')),
                'meta_length' => strlen($request->input('meta_description', '')),
                'content_length' => strlen($request->input('content', '')),
                'content_first_100_chars' => substr($request->input('content', ''), 0, 100),
                'keyword_length' => strlen($request->input('target_keyword', '')),
                'content_present' => $request->has('content') ? 'yes' : 'no',
                'method' => $request->method(),
                'is_ajax' => $request->ajax() ? 'yes' : 'no',
                'request_id' => $requestId
            ]);

            $validatedData = $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'content' => 'required|string',
                'target_keyword' => 'required|string|max:100',
            ]);
            
            // Log validated data
            Log::info("Validated content data for store [{$requestId}]:", [
                'title' => strlen($validatedData['title']),
                'meta_description' => strlen($validatedData['meta_description']),
                'content' => strlen($validatedData['content']),
                'target_keyword' => $validatedData['target_keyword'],
            ]);

            // Make sure content is not empty
            if (empty($validatedData['content']) || $validatedData['content'] === '<p><br></p>') {
                // Release the lock before throwing an exception
                Cache::forget($lockKey);
                throw new \Exception('Content cannot be empty');
            }

            // Log before creating content to check for duplicates
            Log::info("Creating new content [{$requestId}]");
            
            $content = new Content($validatedData);
            $content->user_id = Auth::id();
            $content->save();
            
            // Verify content was saved correctly
            $savedContent = Content::find($content->id);
            Log::info("Content created with ID {$content->id} [{$requestId}]:", [
                'id' => $savedContent->id,
                'title' => $savedContent->title,
                'meta_description' => $savedContent->meta_description,
                'content_length' => strlen($savedContent->content),
                'target_keyword' => $savedContent->target_keyword,
            ]);

            // Analyze the content
            Log::info("Starting analysis for content ID {$content->id} [{$requestId}]");
            $seoResult = $this->seoAnalyzerService->analyzeContent($savedContent);
            
            // Log the SEO result
            Log::info("SEO Result created for content ID {$content->id} [{$requestId}]:", [
                'id' => $seoResult->id,
                'content_id' => $seoResult->content_id,
                'overall_score' => $seoResult->overall_score,
                'page_title_score' => $seoResult->page_title_score,
                'meta_description_score' => $seoResult->meta_description_score,
                'content_score' => $seoResult->content_score,
            ]);

            // Release the lock after successful processing
            Cache::forget($lockKey);

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('seo-results.show', $seoResult->id)
                ]);
            }

            return redirect()->route('seo-results.show', $seoResult->id)
                ->with('success', 'Content analyzed successfully!');
        } catch (\Exception $e) {
            // Release the lock if there was an error
            if (isset($requestId)) {
                Cache::forget('content_submission_lock_' . $requestId);
            }
            
            Log::error('Content store error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function edit(Content $content)
    {
        $this->authorize('update', $content);

        return view('contents.edit', compact('content'));
    }

    public function update(Request $request, Content $content)
    {
        try {
            $this->authorize('update', $content);

            // Generate unique request ID for tracking duplicate requests
            $requestId = $request->input('request_id', uniqid('req_'));
            
            // Use the Cache to implement a simple lock mechanism to prevent duplicate submissions
            $lockKey = 'content_update_lock_' . $content->id . '_' . $requestId;
            
            // Check if this request is already being processed
            if (Cache::has($lockKey)) {
                Log::warning("Duplicate update request detected. Aborting. [{$requestId}] for content #{$content->id}");
                
                return response()->json([
                    'success' => false,
                    'message' => 'Your update request is already being processed. Please wait.'
                ], 429); // Too Many Requests
            }
            
            // Set a lock for this request (expires after 30 seconds)
            Cache::put($lockKey, true, 30);

            // Enhanced debugging to trace issue
            Log::info("Content update request started [{$requestId}]:", [
                'id' => $content->id,
                'title_length' => strlen($request->input('title', '')),
                'meta_length' => strlen($request->input('meta_description', '')),
                'content_length' => strlen($request->input('content', '')),
                'content_first_100_chars' => substr($request->input('content', ''), 0, 100),
                'keyword_length' => strlen($request->input('target_keyword', '')),
                'content_present' => $request->has('content') ? 'yes' : 'no',
                'method' => $request->method(),
                'is_ajax' => $request->ajax() ? 'yes' : 'no',
                'request_id' => $requestId
            ]);

            Log::info("Validated content length: " . strlen($request->input('content', '')));

            $validatedData = $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'content' => 'required|string',
                'target_keyword' => 'required|string|max:100',
            ]);

            // Ensure the content is not empty
            if (empty($validatedData['content']) || $validatedData['content'] === '<p><br></p>') {
                // Release the lock before throwing an exception
                Cache::forget($lockKey);
                throw new \Exception('Content cannot be empty');
            }

            // Update the content
            Log::info("Updating content #{$content->id} [{$requestId}]", [
                'validated_content_length' => strlen($validatedData['content'])
            ]);
            
            $content->update($validatedData);
            
            // Verify content was updated correctly
            $updatedContent = Content::find($content->id);
            Log::info("Content updated successfully #{$content->id} [{$requestId}]", [
                'content_length' => strlen($updatedContent->content),
                'title' => $updatedContent->title,
                'meta_description' => $updatedContent->meta_description,
                'target_keyword' => $updatedContent->target_keyword
            ]);

            // Check if reanalysis is requested
            if ($request->has('reanalyze') && $request->input('reanalyze') == 'true') {
                Log::info("Reanalyzing content #{$content->id} [{$requestId}]");
                $seoResult = $this->seoAnalyzerService->analyzeContent($updatedContent);
                
                Log::info("SEO Result updated for content #{$content->id} [{$requestId}]", [
                    'seo_result_id' => $seoResult->id,
                    'overall_score' => $seoResult->overall_score
                ]);
                
                // Set the redirect URL to the new SEO result
                $redirectUrl = route('seo-results.show', $seoResult->id);
            } else {
                // If no reanalysis, just redirect to content index
                $redirectUrl = route('contents.index');
            }

            // Release the lock after successful processing
            Cache::forget($lockKey);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Content updated successfully!',
                    'redirect' => $redirectUrl
                ]);
            }

            // For non-AJAX requests, redirect appropriately
            if (isset($seoResult)) {
                return redirect()->route('seo-results.show', $seoResult->id)
                    ->with('success', 'Content updated and analyzed successfully!');
            } else {
                return redirect()->route('contents.index')
                    ->with('success', 'Content updated successfully!');
            }
        } catch (\Exception $e) {
            // Release the lock if there was an error
            if (isset($requestId) && isset($content)) {
                Cache::forget('content_update_lock_' . $content->id . '_' . $requestId);
            }
            
            Log::error('Content update error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function destroy(Content $content)
    {
        try {
            $this->authorize('delete', $content);

            $content->delete();

            return redirect()->route('contents.index')
                ->with('success', 'Content deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Content delete error: ' . $e->getMessage());

            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
