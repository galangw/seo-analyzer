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
                    'min' => 30,
                    'max' => 60,
                    'description' => 'Title length should be between 30-60 characters',
                ],
            ],
        ],
        'meta_description' => [
            'weight' => 0.05,
            'sub_criteria' => [
                'keyword_exists' => [
                    'weight' => 0.60, // 3% of total
                    'description' => 'Target keyword must exist in meta description',
                ],
                'description_length' => [
                    'weight' => 0.40, // 2% of total
                    'min' => 120,
                    'max' => 160,
                    'description' => 'Meta description length should be between 120-160 characters',
                ],
            ],
        ],
        'content' => [
            'weight' => 0.75,
            'sub_criteria' => [
                'word_count' => [
                    'weight' => 0.30, // 22.5% of total
                    'min' => 300,
                    'good' => 600,
                    'description' => 'Content should have at least 300 words, ideally 600+',
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
                    'min' => 1,
                    'good' => 3,
                    'description' => 'Content should have at least one internal link',
                ],
                'keyword_density' => [
                    'weight' => 0.30, // 22.5% of total
                    'min' => 0.5,
                    'max' => 2.5,
                    'description' => 'Keyword density should be between 0.5% and 2.5%',
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
