<?php

namespace App\Services;

use App\Models\Content;
use App\Models\SeoResult;
use DOMDocument;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SeoAnalyzerService
{
    /**
     * Analyze content and save results to database
     */
    public function analyzeContent(Content $content)
    {
        try {
            // Get analysis results
            $results = $this->analyzeAllComponents($content->title, $content->meta_description, $content->content, $content->target_keyword);

            // Create or update SEO result
            $seoResult = SeoResult::updateOrCreate(
                ['content_id' => $content->id],
                [
                    'page_title_score' => $results['page_title']['score'] * 100,
                    'meta_description_score' => $results['meta_description']['score'] * 100,
                    'content_score' => $results['content']['score'] * 100,
                    'overall_score' => $results['overall_score'] * 100,
                    'detail_score' => $results,
                ]
            );

            return $seoResult;
        } catch (\Exception $e) {
            Log::error('SEO Analysis Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Perform real-time analysis without saving to database
     */
    public function performRealTimeAnalysis($title, $metaDescription, $content, $targetKeyword)
    {
        try {
            // Get analysis results (partial, for quick real-time analysis)
            $titleAnalysis = $this->analyzeTitleComponent($title, $targetKeyword);
            $metaAnalysis = $this->analyzeMetaComponent($metaDescription, $targetKeyword);
            $contentAnalysis = $this->analyzeContentComponent($content, $targetKeyword);

            // Calculate overall score
            $overallScore = ($titleAnalysis['score'] * 0.25) + ($metaAnalysis['score'] * 0.25) + ($contentAnalysis['score'] * 0.5);

            // Format feedback for quick display
            $titleFeedback = $this->getComponentFeedback($titleAnalysis);
            $metaFeedback = $this->getComponentFeedback($metaAnalysis);
            $contentFeedback = $this->getComponentFeedback($contentAnalysis);

            return [
                'score' => $overallScore * 100,
                'title_score' => $titleAnalysis['score'] * 100,
                'meta_score' => $metaAnalysis['score'] * 100,
                'content_score' => $contentAnalysis['score'] * 100,
                'title_feedback' => $titleFeedback,
                'meta_feedback' => $metaFeedback,
                'content_feedback' => $contentFeedback,
            ];
        } catch (\Exception $e) {
            Log::error('Real-time SEO Analysis Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Analyze all components of content
     */
    private function analyzeAllComponents($title, $metaDescription, $content, $targetKeyword)
    {
        $titleAnalysis = $this->analyzeTitleComponent($title, $targetKeyword);
        $metaAnalysis = $this->analyzeMetaComponent($metaDescription, $targetKeyword);
        $contentAnalysis = $this->analyzeContentComponent($content, $targetKeyword);

        // Calculate overall score with weights
        $overallScore = ($titleAnalysis['score'] * 0.25) + ($metaAnalysis['score'] * 0.25) + ($contentAnalysis['score'] * 0.5);

        // Generate recommendations
        $recommendations = $this->generateRecommendations($titleAnalysis, $metaAnalysis, $contentAnalysis, $title, $metaDescription, $content);

        return [
            'page_title' => $titleAnalysis,
            'meta_description' => $metaAnalysis,
            'content' => $contentAnalysis,
            'overall_score' => $overallScore,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze title component
     */
    private function analyzeTitleComponent($title, $targetKeyword)
    {
        $score = 0;
        $details = [];

        // Check keyword in title
        $keywordInTitle = Str::contains(strtolower($title), strtolower($targetKeyword));
        $keywordScore = $keywordInTitle ? 1.0 : 0.0;
        $details['keyword_in_title'] = [
            'score' => $keywordScore,
            'description' => $keywordInTitle
                ? 'Great! Your title contains the target keyword.'
                : 'Consider adding your target keyword to the title.',
        ];

        // Check title length
        $titleLength = strlen($title);
        $lengthScore = 0;
        if ($titleLength >= 30 && $titleLength <= 60) {
            $lengthScore = 1.0;
        } elseif (($titleLength >= 20 && $titleLength < 30) || ($titleLength > 60 && $titleLength <= 70)) {
            $lengthScore = 0.5;
        }

        $details['title_length'] = [
            'score' => $lengthScore,
            'description' => $titleLength >= 30 && $titleLength <= 60
                ? 'Perfect title length!'
                : "Current length: $titleLength characters. Aim for 30-60 characters.",
        ];

        // Calculate title component score
        $score = ($keywordScore * 0.6) + ($lengthScore * 0.4);

        return [
            'score' => $score,
            'details' => $details,
        ];
    }

    /**
     * Analyze meta description component
     */
    private function analyzeMetaComponent($metaDescription, $targetKeyword)
    {
        $score = 0;
        $details = [];

        // Check keyword in meta description
        $keywordInMeta = Str::contains(strtolower($metaDescription), strtolower($targetKeyword));
        $keywordScore = $keywordInMeta ? 1.0 : 0.0;
        $details['keyword_in_meta'] = [
            'score' => $keywordScore,
            'description' => $keywordInMeta
                ? 'Good! Meta description includes target keyword.'
                : 'Add your target keyword to the meta description.',
        ];

        // Check meta description length
        $metaLength = strlen($metaDescription);
        $lengthScore = 0;
        if ($metaLength >= 120 && $metaLength <= 160) {
            $lengthScore = 1.0;
        } elseif (($metaLength >= 80 && $metaLength < 120) || ($metaLength > 160 && $metaLength <= 200)) {
            $lengthScore = 0.5;
        }

        $details['meta_length'] = [
            'score' => $lengthScore,
            'description' => $metaLength >= 120 && $metaLength <= 160
                ? 'Perfect meta description length!'
                : "Current length: $metaLength characters. Aim for 120-160 characters.",
        ];

        // Calculate meta component score
        $score = ($keywordScore * 0.5) + ($lengthScore * 0.5);

        return [
            'score' => $score,
            'details' => $details,
        ];
    }

    /**
     * Analyze content component
     */
    private function analyzeContentComponent($content, $targetKeyword)
    {
        $score = 0;
        $details = [];

        // Clean HTML tags for text analysis
        $plainText = strip_tags($content);

        // Count words
        $words = preg_split('/\s+/', $plainText);
        $wordCount = count($words);

        // Calculate word count score
        $wordCountScore = 0;
        if ($wordCount >= 600) {
            $wordCountScore = 1.0;
        } elseif ($wordCount >= 300 && $wordCount < 600) {
            $wordCountScore = 0.5;
        } elseif ($wordCount >= 100 && $wordCount < 300) {
            $wordCountScore = 0.2;
        }

        $details['word_count'] = [
            'score' => $wordCountScore,
            'description' => $wordCount >= 600
                ? "Great word count of $wordCount words."
                : "Current word count: $wordCount. Aim for at least 600 words for comprehensive content.",
        ];

        // Calculate keyword density
        $keywordCount = substr_count(strtolower($plainText), strtolower($targetKeyword));
        $keywordDensity = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;

        $keywordDensityScore = 0;
        if ($keywordDensity >= 0.5 && $keywordDensity <= 2.5) {
            $keywordDensityScore = 1.0;
        } elseif (($keywordDensity > 0 && $keywordDensity < 0.5) || ($keywordDensity > 2.5 && $keywordDensity <= 4)) {
            $keywordDensityScore = 0.5;
        }

        $details['keyword_density'] = [
            'score' => $keywordDensityScore,
            'description' => $keywordDensity >= 0.5 && $keywordDensity <= 2.5
                ? "Good keyword density of " . number_format($keywordDensity, 2) . "%."
                : "Current keyword density: " . number_format($keywordDensity, 2) . "%. Aim for 0.5% to 2.5%.",
        ];

        // Check for headings (h1, h2, h3)
        $headingsCount = $this->countHeadings($content);
        $headingsScore = $headingsCount > 0 ? 1.0 : 0.0;

        $details['headings'] = [
            'score' => $headingsScore,
            'description' => $headingsCount > 0
                ? "Good use of headings ($headingsCount found)."
                : "Consider adding headings (H1, H2, H3) to structure your content.",
        ];

        // Calculate content component score
        $score = ($wordCountScore * 0.4) + ($keywordDensityScore * 0.4) + ($headingsScore * 0.2);

        return [
            'score' => $score,
            'details' => $details,
        ];
    }

    /**
     * Count headings in HTML content
     */
    private function countHeadings($content)
    {
        $headingCount = 0;

        // Count h1, h2, h3 tags
        $headingCount += substr_count(strtolower($content), '<h1');
        $headingCount += substr_count(strtolower($content), '<h2');
        $headingCount += substr_count(strtolower($content), '<h3');

        return $headingCount;
    }

    /**
     * Generate recommendations based on analysis
     */
    private function generateRecommendations($titleAnalysis, $metaAnalysis, $contentAnalysis, $title, $metaDescription, $content)
    {
        $recommendations = [];

        // Title recommendations
        if ($titleAnalysis['details']['keyword_in_title']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Page Title',
                'criteria' => 'Keyword Usage',
                'description' => 'Include your target keyword in the page title.',
                'actual' => "Current title: '$title'",
                'recommended' => "Consider adding your target keyword to the title.",
            ];
        }

        if ($titleAnalysis['details']['title_length']['score'] < 1) {
            $titleLength = strlen($title);
            $recommendations[] = [
                'section' => 'Page Title',
                'criteria' => 'Title Length',
                'description' => 'Optimize title length to be between 30-60 characters.',
                'actual' => "Current length: $titleLength characters.",
                'recommended' => "Aim for 30-60 characters.",
            ];
        }

        // Meta description recommendations
        if ($metaAnalysis['details']['keyword_in_meta']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Meta Description',
                'criteria' => 'Keyword Usage',
                'description' => 'Include your target keyword in the meta description.',
                'actual' => "Current meta description doesn't contain the target keyword.",
                'recommended' => "Add your target keyword to increase relevance.",
            ];
        }

        if ($metaAnalysis['details']['meta_length']['score'] < 1) {
            $metaLength = strlen($metaDescription);
            $recommendations[] = [
                'section' => 'Meta Description',
                'criteria' => 'Meta Description Length',
                'description' => 'Optimize meta description length to be between 120-160 characters.',
                'actual' => "Current length: $metaLength characters.",
                'recommended' => "Aim for 120-160 characters.",
            ];
        }

        // Content recommendations
        if (isset($contentAnalysis['details']['word_count']) && $contentAnalysis['details']['word_count']['score'] < 1) {
            // Fix: Get word count properly
            $plainText = strip_tags($content);
            $words = preg_split('/\s+/', $plainText);
            $wordCount = count($words);

            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Word Count',
                'description' => 'Increase the content length for better coverage of the topic.',
                'actual' => "Current word count: " . $wordCount,
                'recommended' => "Aim for at least 600 words.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_density']) && $contentAnalysis['details']['keyword_density']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Keyword Density',
                'description' => 'Adjust the keyword density to be between 0.5% and 2.5%.',
                'actual' => $contentAnalysis['details']['keyword_density']['description'],
                'recommended' => "Aim for 0.5% to 2.5% keyword density.",
            ];
        }

        if (isset($contentAnalysis['details']['headings']) && $contentAnalysis['details']['headings']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Headings Structure',
                'description' => 'Add headings to structure your content better.',
                'actual' => "No headings found.",
                'recommended' => "Use H1, H2, and H3 tags to organize your content.",
            ];
        }

        return $recommendations;
    }

    /**
     * Generate simple feedback for a component for real-time analysis
     */
    private function getComponentFeedback($analysis)
    {
        $feedback = '';
        foreach ($analysis['details'] as $detail) {
            $feedback .= $detail['description'] . ' ';
        }

        return trim($feedback);
    }
}
