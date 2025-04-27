<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\SeoResult;
use App\Services\SeoAnalyzerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeoResultController extends Controller
{
    private $seoAnalyzerService;

    public function __construct(SeoAnalyzerService $seoAnalyzerService)
    {
        $this->middleware('auth');
        $this->seoAnalyzerService = $seoAnalyzerService;
    }

    public function index()
    {
        $seoResults = SeoResult::with('content')
            ->whereHas('content', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('seo-results.index', compact('seoResults'));
    }

    public function show(SeoResult $seoResult)
    {
        $this->authorize('view', $seoResult);

        $content = $seoResult->content;

        return view('seo-results.show', compact('seoResult', 'content'));
    }

    public function reanalyze(Content $content)
    {
        $this->authorize('update', $content);

        $seoResult = $this->seoAnalyzerService->analyzeContent($content);

        return redirect()->route('seo-results.show', $seoResult->id)
            ->with('success', 'Content reanalyzed successfully!');
    }
}
