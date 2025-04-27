<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Services\SeoAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContentController extends Controller
{
    private $seoAnalyzerService;

    public function __construct(SeoAnalyzerService $seoAnalyzerService)
    {
        $this->middleware('auth');
        $this->seoAnalyzerService = $seoAnalyzerService;
    }

    public function index()
    {
        $contents = Auth::user()->contents()
            ->with('latestSeoResult')
            ->latest()
            ->paginate(10);

        return view('contents.index', compact('contents'));
    }

    public function create()
    {
        return view('contents.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'content' => 'required|string',
                'target_keyword' => 'required|string|max:100',
            ]);

            $content = new Content($validatedData);
            $content->user_id = Auth::id();
            $content->save();

            // Analyze the content
            $seoResult = $this->seoAnalyzerService->analyzeContent($content);

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

            $validatedData = $request->validate([
                'title' => 'required',
                'meta_description' => 'required',
                'content' => 'required|string',
                'target_keyword' => 'required|string|max:100',
            ]);

            $content->update($validatedData);

            // Analyze the updated content
            $seoResult = $this->seoAnalyzerService->analyzeContent($content);

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('seo-results.show', $seoResult->id)
                ]);
            }

            return redirect()->route('seo-results.show', $seoResult->id)
                ->with('success', 'Content updated and analyzed successfully!');
        } catch (\Exception $e) {
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
