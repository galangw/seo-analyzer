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
            $overallScore = ($titleAnalysis['score'] * 0.20) + ($metaAnalysis['score'] * 0.05) + ($contentAnalysis['score'] * 0.75);

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
        $overallScore = ($titleAnalysis['score'] * 0.20) + ($metaAnalysis['score'] * 0.05) + ($contentAnalysis['score'] * 0.75);

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
            'weight' => 0.7,
            'description' => $keywordInTitle
                ? 'Great! Your title contains the target keyword.'
                : 'Consider adding your target keyword to the title.',
        ];

        // Check title length
        $titleLength = strlen($title);
        $lengthScore = 0;
        if ($titleLength >= 75 && $titleLength <= 95) {
            $lengthScore = 1.0;
        } elseif (($titleLength >= 40 && $titleLength < 75) || ($titleLength > 95 && $titleLength <= 120)) {
            $lengthScore = 0.5;
        }

        $details['title_length'] = [
            'score' => $lengthScore,
            'weight' => 0.3,
            'description' => $titleLength >= 75 && $titleLength <= 95
                ? 'Perfect title length!'
                : "Current length: $titleLength characters. Aim for 75-95 characters for optimal visibility.",
            'actual' => $titleLength,
            'recommended' => '75-95 characters (40-120 acceptable)',
        ];

        // Calculate title component score
        $score = ($keywordScore * 0.7) + ($lengthScore * 0.3);

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
            'weight' => 0.5,
            'description' => $keywordInMeta
                ? 'Good! Meta description includes target keyword.'
                : 'Add your target keyword to the meta description.',
        ];

        // Check meta description length
        $metaLength = strlen($metaDescription);
        $lengthScore = 0;
        if ($metaLength >= 146 && $metaLength <= 160) {
            $lengthScore = 1.0;
        } elseif ($metaLength >= 100 && $metaLength < 146) {
            $lengthScore = 0.5;
        }

        $details['meta_length'] = [
            'score' => $lengthScore,
            'weight' => 0.5,
            'description' => $metaLength >= 146 && $metaLength <= 160
                ? 'Perfect meta description length!'
                : "Current length: $metaLength characters. Aim for 146-160 characters.",
            'actual' => $metaLength,
            'recommended' => '146-160 characters (100-160 acceptable)',
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
        if ($wordCount >= 1200) {
            $wordCountScore = 1.0;
        } elseif ($wordCount >= 700 && $wordCount < 1200) {
            $wordCountScore = 0.5;
        }

        $details['word_count'] = [
            'score' => $wordCountScore,
            'weight' => 0.3,
            'description' => $wordCount >= 1200
                ? "Great word count of $wordCount words."
                : "Current word count: $wordCount. Aim for at least 1200 words for comprehensive content.",
            'actual' => $wordCount,
            'recommended' => 'At least 1200 words (minimum 700)',
        ];

        // Check keyword in first paragraph
        $paragraphs = $this->getParagraphs($content);
        $firstParagraph = $paragraphs[0] ?? '';
        $keywordInFirstParagraphScore = stripos($firstParagraph, $targetKeyword) !== false ? 1.0 : 0.0;

        $details['keyword_first_paragraph'] = [
            'score' => $keywordInFirstParagraphScore,
            'weight' => 0.1,
            'description' => $keywordInFirstParagraphScore === 1.0
                ? "Great! Your first paragraph contains the target keyword."
                : "Add your target keyword to the first paragraph to improve SEO.",
        ];

        // Check keyword in last paragraph
        $lastParagraph = end($paragraphs) ?: '';
        $keywordInLastParagraphScore = stripos($lastParagraph, $targetKeyword) !== false ? 1.0 : 0.0;

        $details['keyword_last_paragraph'] = [
            'score' => $keywordInLastParagraphScore,
            'weight' => 0.1,
            'description' => $keywordInLastParagraphScore === 1.0
                ? "Good! Your last paragraph contains the target keyword."
                : "Consider adding your target keyword to the last paragraph to improve SEO.",
        ];

        // Check keyword in image alt text
        $keywordInAltScore = $this->checkKeywordInImageAlt($content, $targetKeyword) ? 1.0 : 0.0;

        $details['keyword_in_img_alt'] = [
            'score' => $keywordInAltScore,
            'weight' => 0.1,
            'description' => $keywordInAltScore === 1.0
                ? "Well done! Your images include alt text with the target keyword."
                : "Add your target keyword to at least one image alt text.",
        ];

        // Calculate keyword density
        $keywordCount = substr_count(strtolower($plainText), strtolower($targetKeyword));
        $keywordDensity = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;

        $keywordDensityScore = 0;
        if ($keywordDensity >= 1.0 && $keywordDensity <= 2.0) {
            $keywordDensityScore = 1.0;
        } elseif (($keywordDensity > 0 && $keywordDensity < 1.0) || ($keywordDensity > 2.0 && $keywordDensity <= 4.0)) {
            $keywordDensityScore = 0.5;
        }

        $details['keyword_density'] = [
            'score' => $keywordDensityScore,
            'weight' => 0.3,
            'description' => $keywordDensity >= 1.0 && $keywordDensity <= 2.0
                ? "Good keyword density of " . number_format($keywordDensity, 2) . "%."
                : "Current keyword density: " . number_format($keywordDensity, 2) . "%. Aim for 1.0% to 2.0%.",
            'actual' => number_format($keywordDensity, 2) . '%',
            'recommended' => '1.0% to 2.0% (0.5% to 4.0% acceptable)',
        ];

        // Internal links check
        $internalLinkCount = $this->countInternalLinks($content);
        $internalLinkPercentage = $wordCount > 0 ? ($internalLinkCount / $wordCount) * 100 : 0;

        $internalLinkScore = 0;
        if ($internalLinkPercentage >= 0.5 && $internalLinkPercentage <= 2.0) {
            $internalLinkScore = 1.0;
        } elseif ($internalLinkPercentage > 0 && $internalLinkPercentage < 0.5) {
            $internalLinkScore = 0.5;
        }

        $details['internal_links'] = [
            'score' => $internalLinkScore,
            'weight' => 0.1,
            'description' => $internalLinkPercentage >= 0.5 && $internalLinkPercentage <= 2.0
                ? "Great internal linking: " . number_format($internalLinkPercentage, 2) . "% of content."
                : "Internal link percentage: " . number_format($internalLinkPercentage, 2) . "%. Aim for 0.5% to 2.0%.",
            'actual' => $internalLinkCount . ' links (' . number_format($internalLinkPercentage, 2) . '%)',
            'recommended' => '0.5% to 2.0% of content',
        ];

        // Calculate content component score - uses the weights defined in config
        $score = ($wordCountScore * 0.3) +
                ($keywordInFirstParagraphScore * 0.1) +
                ($keywordInLastParagraphScore * 0.1) +
                ($keywordInAltScore * 0.1) +
                ($internalLinkScore * 0.1) +
                ($keywordDensityScore * 0.3);

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
     * Get paragraphs from content
     */
    private function getParagraphs($content)
    {
        $strippedContent = strip_tags($content, '<p>');
        preg_match_all('/<p>(.*?)<\/p>/s', $strippedContent, $matches);

        if (empty($matches[1])) {
            // If no <p> tags, split by double newlines
            return preg_split('/\r\n\r\n|\n\n/', strip_tags($content));
        }

        return $matches[1];
    }

    /**
     * Check if keyword exists in image alt attributes
     */
    private function checkKeywordInImageAlt($content, $keyword)
    {
        preg_match_all('/<img[^>]*alt=["\'](.*?)["\'][^>]*>/i', $content, $matches);

        if (empty($matches[1])) {
            return false;
        }

        foreach ($matches[1] as $alt) {
            if (stripos($alt, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Count internal links in content
     */
    private function countInternalLinks($content)
    {
        preg_match_all('/<a[^>]*href=["\'](https?:\/\/[^"\']*|\/[^"\']*)["\'][^>]*>/i', $content, $matches);
        return count($matches[0]);
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
                'description' => 'Optimize title length to be between 75-95 characters.',
                'actual' => "Current length: $titleLength characters.",
                'recommended' => "Aim for 75-95 characters for optimal visibility.",
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
                'description' => 'Optimize meta description length to be between 146-160 characters.',
                'actual' => "Current length: $metaLength characters.",
                'recommended' => "Aim for 146-160 characters for optimal visibility.",
            ];
        }

        // Content recommendations
        if (isset($contentAnalysis['details']['word_count']) && $contentAnalysis['details']['word_count']['score'] < 1) {
            // Get word count properly
            $plainText = strip_tags($content);
            $words = preg_split('/\s+/', $plainText);
            $wordCount = count($words);

            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Word Count',
                'description' => 'Increase the content length for better coverage of the topic.',
                'actual' => "Current word count: " . $wordCount,
                'recommended' => "Aim for at least 1200 words for comprehensive content.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_density']) && $contentAnalysis['details']['keyword_density']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Keyword Density',
                'description' => 'Adjust the keyword density to be between 1.0% and 2.0%.',
                'actual' => $contentAnalysis['details']['keyword_density']['actual'],
                'recommended' => "Aim for 1.0% to 2.0% keyword density.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_first_paragraph']) && $contentAnalysis['details']['keyword_first_paragraph']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'First Paragraph',
                'description' => 'Include target keyword in the first paragraph.',
                'actual' => "Keyword not found in first paragraph.",
                'recommended' => "Add your target keyword naturally to the first paragraph.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_last_paragraph']) && $contentAnalysis['details']['keyword_last_paragraph']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Last Paragraph',
                'description' => 'Include target keyword in the last paragraph.',
                'actual' => "Keyword not found in last paragraph.",
                'recommended' => "Add your target keyword naturally to the last paragraph.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_in_img_alt']) && $contentAnalysis['details']['keyword_in_img_alt']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Image Alt Text',
                'description' => 'Include target keyword in at least one image alt attribute.',
                'actual' => "No images with target keyword in alt attribute found.",
                'recommended' => "Add target keyword to at least one relevant image alt text.",
            ];
        }

        if (isset($contentAnalysis['details']['internal_links']) && $contentAnalysis['details']['internal_links']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Internal Links',
                'description' => 'Optimize internal linking percentage in content.',
                'actual' => $contentAnalysis['details']['internal_links']['actual'],
                'recommended' => "Internal links should represent 0.5% to 2.0% of your content.",
            ];
        }

        return $recommendations;
    }

    /**
     * Generate simple feedback for a component for real-time analysis
     */
    private function getComponentFeedback($analysis)
    {
        $feedback = [];
        foreach ($analysis['details'] as $key => $detail) {
            $feedbackItem = [
                'criteria' => $key,
                'description' => $detail['description'],
                'score' => $detail['score'],
            ];

            if (isset($detail['actual'])) {
                $feedbackItem['actual'] = $detail['actual'];
            }

            if (isset($detail['recommended'])) {
                $feedbackItem['recommended'] = $detail['recommended'];
            }

            $feedback[] = $feedbackItem;
        }

        return $feedback;
    }
}
