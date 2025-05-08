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

        // Check if keyword exists in title
        $keywordExistsScore = stripos($title, $keyword) !== false
            ? $this->ratingScale['good']
            : $this->ratingScale['bad'];
        $scores['keyword_exists'] = [
            'score' => $keywordExistsScore,
            'weight' => $subCriteria['keyword_exists']['weight'],
            'value' => $keywordExistsScore * $subCriteria['keyword_exists']['weight'],
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

        // Check if keyword exists in meta description
        $keywordExistsScore = stripos($metaDescription, $keyword) !== false
            ? $this->ratingScale['good']
            : $this->ratingScale['bad'];
        $scores['keyword_exists'] = [
            'score' => $keywordExistsScore,
            'weight' => $subCriteria['keyword_exists']['weight'],
            'value' => $keywordExistsScore * $subCriteria['keyword_exists']['weight'],
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
        $keywordInFirstParagraphScore = stripos($firstParagraph, $keyword) !== false
            ? $this->ratingScale['good']
            : $this->ratingScale['bad'];
        $scores['keyword_first_paragraph'] = [
            'score' => $keywordInFirstParagraphScore,
            'weight' => $subCriteria['keyword_first_paragraph']['weight'],
            'value' => $keywordInFirstParagraphScore * $subCriteria['keyword_first_paragraph']['weight'],
        ];

        // Keyword in last paragraph
        $lastParagraph = end($paragraphs) ?: '';
        $keywordInLastParagraphScore = stripos($lastParagraph, $keyword) !== false
            ? $this->ratingScale['good']
            : $this->ratingScale['bad'];
        $scores['keyword_last_paragraph'] = [
            'score' => $keywordInLastParagraphScore,
            'weight' => $subCriteria['keyword_last_paragraph']['weight'],
            'value' => $keywordInLastParagraphScore * $subCriteria['keyword_last_paragraph']['weight'],
        ];

        // Keyword in image alt text
        $keywordInAltScore = $this->checkKeywordInImageAlt($content, $keyword)
            ? $this->ratingScale['good']
            : $this->ratingScale['bad'];
        $scores['keyword_in_img_alt'] = [
            'score' => $keywordInAltScore,
            'weight' => $subCriteria['keyword_in_img_alt']['weight'],
            'value' => $keywordInAltScore * $subCriteria['keyword_in_img_alt']['weight'],
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
        $keywordDensity = $this->calculateKeywordDensity($content, $keyword);
        $keywordDensityScore = 0;
        if ($keywordDensity >= 1.0 && $keywordDensity <= 2.0) {
            $keywordDensityScore = $this->ratingScale['good']; // 100%
        } elseif (($keywordDensity > 0 && $keywordDensity < 1.0) || ($keywordDensity > 2.0 && $keywordDensity <= 4.0)) {
            $keywordDensityScore = $this->ratingScale['improve']; // 50%
        } else {
            $keywordDensityScore = $this->ratingScale['bad']; // 0%
        }
        
        $scores['keyword_density'] = [
            'score' => $keywordDensityScore,
            'weight' => $subCriteria['keyword_density']['weight'],
            'value' => $keywordDensityScore * $subCriteria['keyword_density']['weight'],
            'actual' => number_format($keywordDensity, 2) . '%',
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

        // Count keyword occurrences
        $keywordCount = preg_match_all('/\b' . preg_quote($keyword, '/') . '\b/i', $strippedContent);

        // Calculate density percentage
        return ($keywordCount / $wordCount) * 100;
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
