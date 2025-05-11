<?php

namespace App\Services;

class ScoreCalculatorService
{
    private $criteria;
    private $ratingScale;

    public function __construct()
    {
        $this->criteria = config('seo_criteria.criteria');
        $this->ratingScale = config('seo_criteria.rating_scale');
    }

    public function calculateTitleScore($title, $keyword)
    {
        $subCriteria = $this->criteria['page_title']['sub_criteria'];
        $scores = [];

        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $keyword));
        $keywordCount = count($keywords);
        
        // Check keywords in title
        $keywordsInTitle = 0;
        foreach ($keywords as $kw) {
            if (stripos($title, $kw) !== false) {
                $keywordsInTitle++;
            }
        }
        
        // Calculate score based on percentage of keywords found
        $keywordScore = 0;
        $keywordPercentage = $keywordCount > 0 ? ($keywordsInTitle / $keywordCount) * 100 : 0;
        
        if ($keywordPercentage == 100) {
            $keywordScore = $this->ratingScale['good']; // 100% - all keywords present
        } elseif ($keywordPercentage >= 50) {
            $keywordScore = $this->ratingScale['improve']; // 50% - at least half of keywords present
        } else {
            $keywordScore = $this->ratingScale['bad']; // 0% - less than half of keywords present
        }
        
        $scores['keyword_exists'] = [
            'score' => $keywordScore,
            'weight' => $subCriteria['keyword_exists']['weight'],
            'value' => $keywordScore * $subCriteria['keyword_exists']['weight'],
            'actual' => "{$keywordsInTitle}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the title',
        ];

        // Check title length
        $titleLength = strlen($title);
        $lengthScore = 0;
        if ($titleLength >= 75 && $titleLength <= 95) {
            $lengthScore = $this->ratingScale['good']; // 100%
        } elseif (($titleLength >= 40 && $titleLength < 75) || ($titleLength > 95 && $titleLength <= 120)) {
            $lengthScore = $this->ratingScale['improve']; // 50%
        } else {
            $lengthScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['title_length'] = [
            'score' => $lengthScore,
            'weight' => $subCriteria['title_length']['weight'],
            'value' => $lengthScore * $subCriteria['title_length']['weight'],
            'actual' => $titleLength,
            'recommended' => '75-95 characters (40-120 acceptable)',
        ];

        $totalScore = array_sum(array_column($scores, 'value'));
        return [
            'score' => $totalScore,
            'details' => $scores,
        ];
    }

    public function calculateMetaDescriptionScore($metaDescription, $keyword)
    {
        $subCriteria = $this->criteria['meta_description']['sub_criteria'];
        $scores = [];

        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $keyword));
        $keywordCount = count($keywords);
        
        // Check keywords in meta description
        $keywordsInMeta = 0;
        foreach ($keywords as $kw) {
            if (stripos($metaDescription, $kw) !== false) {
                $keywordsInMeta++;
            }
        }
        
        // Calculate score based on percentage of keywords found
        $keywordScore = 0;
        $keywordPercentage = $keywordCount > 0 ? ($keywordsInMeta / $keywordCount) * 100 : 0;
        
        if ($keywordPercentage == 100) {
            $keywordScore = $this->ratingScale['good']; // 100% - all keywords present
        } elseif ($keywordPercentage >= 50) {
            $keywordScore = $this->ratingScale['improve']; // 50% - at least half of keywords present
        } else {
            $keywordScore = $this->ratingScale['bad']; // 0% - less than half of keywords present
        }
        
        $scores['keyword_exists'] = [
            'score' => $keywordScore,
            'weight' => $subCriteria['keyword_exists']['weight'],
            'value' => $keywordScore * $subCriteria['keyword_exists']['weight'],
            'actual' => "{$keywordsInMeta}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the meta description',
        ];

        // Check meta description length
        $descLength = strlen($metaDescription);
        $lengthScore = 0;
        if ($descLength >= 146 && $descLength <= 160) {
            $lengthScore = $this->ratingScale['good']; // 100%
        } elseif ($descLength >= 100 && $descLength < 146) {
            $lengthScore = $this->ratingScale['improve']; // 50%
        } else {
            $lengthScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['description_length'] = [
            'score' => $lengthScore,
            'weight' => $subCriteria['description_length']['weight'],
            'value' => $lengthScore * $subCriteria['description_length']['weight'],
            'actual' => $descLength,
            'recommended' => '146-160 characters (100-160 acceptable)',
        ];

        $totalScore = array_sum(array_column($scores, 'value'));
        return [
            'score' => $totalScore,
            'details' => $scores,
        ];
    }

    public function calculateContentScore($content, $keyword)
    {
        $subCriteria = $this->criteria['content']['sub_criteria'];
        $scores = [];

        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $keyword));
        $keywordCount = count($keywords);

        // Word count check
        $wordCount = str_word_count(strip_tags($content));
        $wordCountScore = 0;
        if ($wordCount >= 1200) {
            $wordCountScore = $this->ratingScale['good']; // 100%
        } elseif ($wordCount >= 700 && $wordCount < 1200) {
            $wordCountScore = $this->ratingScale['improve']; // 50%
        } else {
            $wordCountScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['word_count'] = [
            'score' => $wordCountScore,
            'weight' => $subCriteria['word_count']['weight'],
            'value' => $wordCountScore * $subCriteria['word_count']['weight'],
            'actual' => $wordCount,
            'recommended' => 'At least 1200 words (minimum 700)',
        ];

        // Keyword in first paragraph
        $paragraphs = $this->getParagraphs($content);
        $firstParagraph = $paragraphs[0] ?? '';
        
        $keywordsInFirstParagraph = 0;
        foreach ($keywords as $kw) {
            if (stripos($firstParagraph, $kw) !== false) {
                $keywordsInFirstParagraph++;
            }
        }
        
        // Calculate score based on percentage of keywords found
        $keywordInFirstParagraphScore = 0;
        $keywordFirstParagraphPercentage = $keywordCount > 0 ? ($keywordsInFirstParagraph / $keywordCount) * 100 : 0;
        
        if ($keywordFirstParagraphPercentage == 100) {
            $keywordInFirstParagraphScore = $this->ratingScale['good']; // 100% - all keywords present
        } elseif ($keywordFirstParagraphPercentage >= 50) {
            $keywordInFirstParagraphScore = $this->ratingScale['improve']; // 50% - at least half of keywords present
        } else {
            $keywordInFirstParagraphScore = $this->ratingScale['bad']; // 0% - less than half of keywords present
        }
        
        $scores['keyword_first_paragraph'] = [
            'score' => $keywordInFirstParagraphScore,
            'weight' => $subCriteria['keyword_first_paragraph']['weight'],
            'value' => $keywordInFirstParagraphScore * $subCriteria['keyword_first_paragraph']['weight'],
            'actual' => "{$keywordsInFirstParagraph}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the first paragraph',
        ];

        // Keyword in last paragraph
        $lastParagraph = end($paragraphs) ?: '';
        
        $keywordsInLastParagraph = 0;
        foreach ($keywords as $kw) {
            if (stripos($lastParagraph, $kw) !== false) {
                $keywordsInLastParagraph++;
            }
        }
        
        // Calculate score based on percentage of keywords found
        $keywordInLastParagraphScore = 0;
        $keywordLastParagraphPercentage = $keywordCount > 0 ? ($keywordsInLastParagraph / $keywordCount) * 100 : 0;
        
        if ($keywordLastParagraphPercentage == 100) {
            $keywordInLastParagraphScore = $this->ratingScale['good']; // 100% - all keywords present
        } elseif ($keywordLastParagraphPercentage >= 50) {
            $keywordInLastParagraphScore = $this->ratingScale['improve']; // 50% - at least half of keywords present
        } else {
            $keywordInLastParagraphScore = $this->ratingScale['bad']; // 0% - less than half of keywords present
        }
        
        $scores['keyword_last_paragraph'] = [
            'score' => $keywordInLastParagraphScore,
            'weight' => $subCriteria['keyword_last_paragraph']['weight'],
            'value' => $keywordInLastParagraphScore * $subCriteria['keyword_last_paragraph']['weight'],
            'actual' => "{$keywordsInLastParagraph}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in the last paragraph',
        ];

        // Keyword in image alt text
        $keywordsInAlt = 0;
        foreach ($keywords as $kw) {
            if ($this->checkKeywordInImageAlt($content, $kw)) {
                $keywordsInAlt++;
            }
        }
        
        // Calculate score based on percentage of keywords found
        $keywordInAltScore = 0;
        $keywordInAltPercentage = $keywordCount > 0 ? ($keywordsInAlt / $keywordCount) * 100 : 0;
        
        if ($keywordInAltPercentage == 100) {
            $keywordInAltScore = $this->ratingScale['good']; // 100% - all keywords present
        } elseif ($keywordInAltPercentage >= 50) {
            $keywordInAltScore = $this->ratingScale['improve']; // 50% - at least half of keywords present
        } else {
            $keywordInAltScore = $this->ratingScale['bad']; // 0% - less than half of keywords present
        }
        
        $scores['keyword_in_img_alt'] = [
            'score' => $keywordInAltScore,
            'weight' => $subCriteria['keyword_in_img_alt']['weight'],
            'value' => $keywordInAltScore * $subCriteria['keyword_in_img_alt']['weight'],
            'actual' => "{$keywordsInAlt}/{$keywordCount} keywords",
            'recommended' => 'Include all target keywords in image alt attributes',
        ];

        // Internal links check - new formula based on percentage
        $internalLinkCount = $this->countInternalLinks($content);
        $internalLinkPercentage = $wordCount > 0 ? ($internalLinkCount / $wordCount) * 100 : 0;
        
        $internalLinkScore = 0;
        if ($internalLinkPercentage >= 0.5 && $internalLinkPercentage <= 2.0) {
            $internalLinkScore = $this->ratingScale['good']; // 100%
        } elseif ($internalLinkPercentage > 0 && $internalLinkPercentage < 0.5) {
            $internalLinkScore = $this->ratingScale['improve']; // 50%
        } else {
            $internalLinkScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['internal_links'] = [
            'score' => $internalLinkScore,
            'weight' => $subCriteria['internal_links']['weight'],
            'value' => $internalLinkScore * $subCriteria['internal_links']['weight'],
            'actual' => $internalLinkCount . ' links (' . number_format($internalLinkPercentage, 2) . '%)',
            'recommended' => '0.5% to 2.0% of content',
        ];

        // Keyword density check
        $keywordDensities = [];
        $plainText = strip_tags($content);
        
        foreach ($keywords as $kw) {
            // Count keyword occurrences per 1200 words
            $kwCount = preg_match_all('/\b' . preg_quote($kw, '/') . '\b/i', $plainText);
            
            // Calculate density per 1200 words
            $scaledWordCount = $wordCount > 0 ? ($wordCount / 1200) : 1;
            $scaledKeywordCount = $kwCount / $scaledWordCount;
            $density = $wordCount > 0 ? ($scaledKeywordCount / 1200) * 100 : 0;
            
            $keywordDensities[] = $density;
        }
        
        // Average density across all keywords
        $avgKeywordDensity = !empty($keywordDensities) ? array_sum($keywordDensities) / count($keywordDensities) : 0;
        
        $keywordDensityScore = 0;
        if ($avgKeywordDensity >= 1.0 && $avgKeywordDensity <= 2.0) {
            $keywordDensityScore = $this->ratingScale['good']; // 100%
        } elseif (($avgKeywordDensity > 0 && $avgKeywordDensity < 1.0) || ($avgKeywordDensity > 2.0 && $avgKeywordDensity <= 4.0)) {
            $keywordDensityScore = $this->ratingScale['improve']; // 50%
        } else {
            $keywordDensityScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['keyword_density'] = [
            'score' => $keywordDensityScore,
            'weight' => $subCriteria['keyword_density']['weight'],
            'value' => $keywordDensityScore * $subCriteria['keyword_density']['weight'],
            'actual' => number_format($avgKeywordDensity, 2) . '%',
            'recommended' => '1.0% to 2.0% (0.5% to 4.0% acceptable)',
        ];

        $totalScore = array_sum(array_column($scores, 'value'));
        return [
            'score' => $totalScore,
            'details' => $scores,
        ];
    }

    public function calculateOverallScore($titleScore, $metaDescriptionScore, $contentScore)
    {
        $weightedTitleScore = $titleScore * $this->criteria['page_title']['weight'];
        $weightedMetaScore = $metaDescriptionScore * $this->criteria['meta_description']['weight'];
        $weightedContentScore = $contentScore * $this->criteria['content']['weight'];

        return $weightedTitleScore + $weightedMetaScore + $weightedContentScore;
    }

    private function calculateRangeScore($value, $min, $max)
    {
        if ($value < $min) {
            // Below minimum - determine how far below
            $distance = ($value / $min);
            return max(0, $distance * $this->ratingScale['improve']);
        } elseif ($value > $max) {
            // Above maximum - determine how far above
            $excess = ($max / $value);
            return max($this->ratingScale['improve'], $excess * $this->ratingScale['good']);
        } else {
            // Within range - perfect score
            return $this->ratingScale['good'];
        }
    }

    private function calculateProgressiveScore($value, $min, $good)
    {
        if ($value < $min) {
            // Below minimum
            return $this->ratingScale['bad'];
        } elseif ($value >= $good) {
            // At or above good threshold
            return $this->ratingScale['good'];
        } else {
            // Between minimum and good - partial score
            $range = $good - $min;
            $position = $value - $min;
            $percentage = $position / $range;
            return $this->ratingScale['improve'] + ($percentage * ($this->ratingScale['good'] - $this->ratingScale['improve']));
        }
    }

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

    private function countInternalLinks($content)
    {
        preg_match_all('/<a[^>]*href=["\'](https?:\/\/[^"\']*|\/[^"\']*)["\'][^>]*>/i', $content, $matches);
        return count($matches[0]);
    }

    private function calculateKeywordDensity($content, $keyword)
    {
        $strippedContent = strip_tags($content);
        $wordCount = str_word_count($strippedContent);

        if ($wordCount === 0) {
            return 0;
        }

        // Split the comma-separated keywords
        $keywords = array_map('trim', explode(',', $keyword));
        $keywordDensities = [];
        
        foreach ($keywords as $kw) {
            // Count keyword occurrences
            $keywordCount = preg_match_all('/\b' . preg_quote($kw, '/') . '\b/i', $strippedContent);
            
            // Calculate density percentage
            $keywordDensities[] = ($keywordCount / $wordCount) * 100;
        }
        
        // Return average density across all keywords
        return !empty($keywordDensities) ? array_sum($keywordDensities) / count($keywordDensities) : 0;
    }

    public function generateRecommendations($titleResult, $metaDescriptionResult, $contentResult)
    {
        $recommendations = [];

        // Check title scores
        foreach ($titleResult['details'] as $key => $detail) {
            if ($detail['score'] < $this->ratingScale['good']) {
                $recommendations[] = $this->getRecommendationForCriteria('page_title', $key, $detail);
            }
        }

        // Check meta description scores
        foreach ($metaDescriptionResult['details'] as $key => $detail) {
            if ($detail['score'] < $this->ratingScale['good']) {
                $recommendations[] = $this->getRecommendationForCriteria('meta_description', $key, $detail);
            }
        }

        // Check content scores
        foreach ($contentResult['details'] as $key => $detail) {
            if ($detail['score'] < $this->ratingScale['good']) {
                $recommendations[] = $this->getRecommendationForCriteria('content', $key, $detail);
            }
        }

        return $recommendations;
    }

    private function getRecommendationForCriteria($section, $criteriaKey, $detail)
    {
        $description = $this->criteria[$section]['sub_criteria'][$criteriaKey]['description'] ?? '';
        $actual = isset($detail['actual']) ? "Current: {$detail['actual']}" : '';
        $recommended = isset($detail['recommended']) ? "Recommended: {$detail['recommended']}" : '';

        return [
            'section' => trans("seo.{$section}"),
            'criteria' => trans("seo.{$criteriaKey}"),
            'description' => $description,
            'actual' => $actual,
            'recommended' => $recommended,
        ];
    }
}
