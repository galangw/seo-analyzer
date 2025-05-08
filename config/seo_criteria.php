<?php

return [
    'criteria' => [
        'page_title' => [
            'weight' => 0.20,
            'sub_criteria' => [
                'keyword_exists' => [
                    'weight' => 0.70, // 14% of total
                    'description' => 'Target keyword must exist in the title',
                ],
                'title_length' => [
                    'weight' => 0.30, // 6% of total
                    'min' => 40,
                    'max' => 95,
                    'description' => 'Title length should be between 75-95 characters, with 40-74 or 95-120 being acceptable',
                ],
            ],
        ],
        'meta_description' => [
            'weight' => 0.05,
            'sub_criteria' => [
                'keyword_exists' => [
                    'weight' => 0.50, // 3% of total
                    'description' => 'Target keyword must exist in meta description',
                ],
                'description_length' => [
                    'weight' => 0.50, // 2% of total
                    'min' => 100,
                    'max' => 160,
                    'description' => 'Meta description length should be between 146-160 characters, with 100-145 being acceptable',
                ],
            ],
        ],
        'content' => [
            'weight' => 0.75,
            'sub_criteria' => [
                'word_count' => [
                    'weight' => 0.30, // 22.5% of total
                    'min' => 700,
                    'good' => 1200,
                    'description' => 'Content should have at least 700 words, ideally 1200+',
                ],
                'keyword_first_paragraph' => [
                    'weight' => 0.10, // 7.5% of total
                    'description' => 'Target keyword should appear in the first paragraph',
                ],
                'keyword_last_paragraph' => [
                    'weight' => 0.10, // 7.5% of total
                    'description' => 'Target keyword should appear in the last paragraph',
                ],
                'keyword_in_img_alt' => [
                    'weight' => 0.10, // 7.5% of total
                    'description' => 'Target keyword should be used in at least one image alt text',
                ],
                'internal_links' => [
                    'weight' => 0.10, // 7.5% of total
                    'min' => 0.5,
                    'good' => 2.0,
                    'description' => 'Internal link percentage should be between 0.5% and 2% of content',
                ],
                'keyword_density' => [
                    'weight' => 0.30, // 22.5% of total
                    'min' => 1.0,
                    'max' => 2.0,
                    'description' => 'Keyword density should be between 1% and 2%, with 0-1% or 2-4% being acceptable',
                ],
            ],
        ],
    ],
    'rating_scale' => [
        'good' => 1.0,     // 100%
        'improve' => 0.5,  // 50%
        'bad' => 0.0,      // 0%
    ],
];
