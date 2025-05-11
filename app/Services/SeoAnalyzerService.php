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
        $recommendations = $this->generateRecommendations($titleAnalysis, $metaAnalysis, $contentAnalysis, $title, $metaDescription, $content, $targetKeyword);

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
        
        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $targetKeyword));
        $keywordCount = count($keywords);
        
        // Check keywords in title
        $keywordsInTitle = 0;
        foreach ($keywords as $keyword) {
            if (Str::contains(strtolower($title), strtolower($keyword))) {
                $keywordsInTitle++;
            }
        }
        
        // Calculate keyword score based on percentage of keywords found
        $keywordScore = 0;
        $keywordPercentage = $keywordCount > 0 ? ($keywordsInTitle / $keywordCount) * 100 : 0;
        
        if ($keywordPercentage == 100) {
            $keywordScore = 1.0; // 100% - all keywords present
        } elseif ($keywordPercentage >= 50) {
            $keywordScore = 0.5; // 50% - at least half of keywords present
        } else {
            $keywordScore = 0.0; // 0% - less than half of keywords present
        }
        
        $details['keyword_in_title'] = [
            'score' => $keywordScore,
            'weight' => 0.7,
            'description' => $keywordsInTitle > 0
                ? "Found {$keywordsInTitle} of {$keywordCount} keywords in title."
                : 'No target keywords found in the title.',
            'actual' => "{$keywordsInTitle}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the title',
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
        
        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $targetKeyword));
        $keywordCount = count($keywords);
        
        // Check keywords in meta description
        $keywordsInMeta = 0;
        foreach ($keywords as $keyword) {
            if (Str::contains(strtolower($metaDescription), strtolower($keyword))) {
                $keywordsInMeta++;
            }
        }
        
        // Calculate keyword score based on percentage of keywords found
        $keywordScore = 0;
        $keywordPercentage = $keywordCount > 0 ? ($keywordsInMeta / $keywordCount) * 100 : 0;
        
        if ($keywordPercentage == 100) {
            $keywordScore = 1.0; // 100% - all keywords present
        } elseif ($keywordPercentage >= 50) {
            $keywordScore = 0.5; // 50% - at least half of keywords present
        } else {
            $keywordScore = 0.0; // 0% - less than half of keywords present
        }

        $details['keyword_in_meta'] = [
            'score' => $keywordScore,
            'weight' => 0.5,
            'description' => $keywordsInMeta > 0
                ? "Found {$keywordsInMeta} of {$keywordCount} keywords in meta description."
                : 'No target keywords found in the meta description.',
            'actual' => "{$keywordsInMeta}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the meta description',
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

        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $targetKeyword));
        $keywordCount = count($keywords);

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

        // Check keywords in first paragraph
        $paragraphs = $this->getParagraphs($content);
        $firstParagraph = $paragraphs[0] ?? '';
        
        $keywordsInFirstParagraph = 0;
        foreach ($keywords as $keyword) {
            if (stripos($firstParagraph, $keyword) !== false) {
                $keywordsInFirstParagraph++;
            }
        }
        
        // Calculate keyword in first paragraph score
        $keywordFirstParagraphScore = 0;
        $keywordFirstParagraphPercentage = $keywordCount > 0 ? ($keywordsInFirstParagraph / $keywordCount) * 100 : 0;
        
        if ($keywordFirstParagraphPercentage == 100) {
            $keywordFirstParagraphScore = 1.0; // 100% - all keywords present
        } elseif ($keywordFirstParagraphPercentage >= 50) {
            $keywordFirstParagraphScore = 0.5; // 50% - at least half of keywords present
        } else {
            $keywordFirstParagraphScore = 0.0; // 0% - less than half of keywords present
        }

        $details['keyword_first_paragraph'] = [
            'score' => $keywordFirstParagraphScore,
            'weight' => 0.1,
            'description' => $keywordsInFirstParagraph > 0
                ? "Found {$keywordsInFirstParagraph} of {$keywordCount} keywords in first paragraph."
                : "No target keywords found in the first paragraph.",
            'actual' => "{$keywordsInFirstParagraph}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the first paragraph',
        ];

        // Check keywords in last paragraph
        $lastParagraph = end($paragraphs) ?: '';
        
        $keywordsInLastParagraph = 0;
        foreach ($keywords as $keyword) {
            if (stripos($lastParagraph, $keyword) !== false) {
                $keywordsInLastParagraph++;
            }
        }
        
        // Calculate keyword in last paragraph score
        $keywordLastParagraphScore = 0;
        $keywordLastParagraphPercentage = $keywordCount > 0 ? ($keywordsInLastParagraph / $keywordCount) * 100 : 0;
        
        if ($keywordLastParagraphPercentage == 100) {
            $keywordLastParagraphScore = 1.0; // 100% - all keywords present
        } elseif ($keywordLastParagraphPercentage >= 50) {
            $keywordLastParagraphScore = 0.5; // 50% - at least half of keywords present
        } else {
            $keywordLastParagraphScore = 0.0; // 0% - less than half of keywords present
        }

        $details['keyword_last_paragraph'] = [
            'score' => $keywordLastParagraphScore,
            'weight' => 0.1,
            'description' => $keywordsInLastParagraph > 0
                ? "Found {$keywordsInLastParagraph} of {$keywordCount} keywords in last paragraph."
                : "No target keywords found in the last paragraph.",
            'actual' => "{$keywordsInLastParagraph}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the last paragraph',
        ];

        // Check keywords in image alt text
        $keywordsInAlt = 0;
        foreach ($keywords as $keyword) {
            if ($this->checkKeywordInImageAlt($content, $keyword)) {
                $keywordsInAlt++;
            }
        }
        
        // Calculate keyword in alt text score
        $keywordInAltScore = 0;
        $keywordInAltPercentage = $keywordCount > 0 ? ($keywordsInAlt / $keywordCount) * 100 : 0;
        
        if ($keywordInAltPercentage == 100) {
            $keywordInAltScore = 1.0; // 100% - all keywords present
        } elseif ($keywordInAltPercentage >= 50) {
            $keywordInAltScore = 0.5; // 50% - at least half of keywords present
        } else {
            $keywordInAltScore = 0.0; // 0% - less than half of keywords present
        }

        $details['keyword_in_img_alt'] = [
            'score' => $keywordInAltScore,
            'weight' => 0.1,
            'description' => $keywordsInAlt > 0
                ? "Found {$keywordsInAlt} of {$keywordCount} keywords in image alt attributes."
                : "No target keywords found in image alt attributes.",
            'actual' => "{$keywordsInAlt}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in image alt attributes',
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

        // Calculate keyword density for each keyword and average them
        $keywordDensities = [];
        foreach ($keywords as $keyword) {
            $keywordCount = substr_count(strtolower($plainText), strtolower($keyword));
            // Calculate per 1200 words as specified in requirements
            $scaledWordCount = $wordCount > 0 ? ($wordCount / 1200) : 1;
            $scaledKeywordCount = $keywordCount / $scaledWordCount;
            $density = $wordCount > 0 ? ($scaledKeywordCount / 1200) * 100 : 0;
            $keywordDensities[] = $density;
        }
        
        // Average density across all keywords
        $avgKeywordDensity = count($keywordDensities) > 0 ? array_sum($keywordDensities) / count($keywordDensities) : 0;
        
        $keywordDensityScore = 0;
        if ($avgKeywordDensity >= 1.0 && $avgKeywordDensity <= 2.0) {
            $keywordDensityScore = 1.0;
        } elseif (($avgKeywordDensity > 0 && $avgKeywordDensity < 1.0) || ($avgKeywordDensity > 2.0 && $avgKeywordDensity <= 4.0)) {
            $keywordDensityScore = 0.5;
        }

        $details['keyword_density'] = [
            'score' => $keywordDensityScore,
            'weight' => 0.3,
            'description' => $avgKeywordDensity >= 1.0 && $avgKeywordDensity <= 2.0
                ? "Good average keyword density of " . number_format($avgKeywordDensity, 2) . "%."
                : "Current average keyword density: " . number_format($avgKeywordDensity, 2) . "%. Aim for 1.0% to 2.0%.",
            'actual' => number_format($avgKeywordDensity, 2) . '%',
            'recommended' => '1.0% to 2.0% (0.5% to 4.0% acceptable)',
        ];

        // Calculate content component score - uses the weights defined in config
        $score = ($wordCountScore * 0.3) +
                ($keywordFirstParagraphScore * 0.1) +
                ($keywordLastParagraphScore * 0.1) +
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
    private function generateRecommendations($titleAnalysis, $metaAnalysis, $contentAnalysis, $title, $metaDescription, $content, $targetKeyword)
    {
        $recommendations = [];
        $keywords = array_map('trim', explode(',', $targetKeyword));
        $keywordCount = count($keywords);

        // Title recommendations
        if ($titleAnalysis['details']['keyword_in_title']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Page Title',
                'criteria' => 'Keyword Usage',
                'description' => 'Include all target keywords in the page title.',
                'actual' => $titleAnalysis['details']['keyword_in_title']['actual'],
                'recommended' => "Consider adding all your target keywords to the title.",
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
                'description' => 'Include all your target keywords in the meta description.',
                'actual' => $metaAnalysis['details']['keyword_in_meta']['actual'],
                'recommended' => "Add all your target keywords to increase relevance.",
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
                'description' => 'Include all target keywords in the first paragraph.',
                'actual' => $contentAnalysis['details']['keyword_first_paragraph']['actual'],
                'recommended' => "Add all your target keywords naturally to the first paragraph.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_last_paragraph']) && $contentAnalysis['details']['keyword_last_paragraph']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Last Paragraph',
                'description' => 'Include all target keywords in the last paragraph.',
                'actual' => $contentAnalysis['details']['keyword_last_paragraph']['actual'],
                'recommended' => "Add all your target keywords naturally to the last paragraph.",
            ];
        }

        if (isset($contentAnalysis['details']['keyword_in_img_alt']) && $contentAnalysis['details']['keyword_in_img_alt']['score'] < 1) {
            $recommendations[] = [
                'section' => 'Content',
                'criteria' => 'Image Alt Text',
                'description' => 'Include all target keywords in at least one image alt attribute.',
                'actual' => $contentAnalysis['details']['keyword_in_img_alt']['actual'],
                'recommended' => "Add all target keywords to at least one relevant image alt text.",
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
