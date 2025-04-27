<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'page_title_score',
        'meta_description_score',
        'content_score',
        'overall_score',
        'detail_score',
    ];

    protected $casts = [
        'detail_score' => 'array',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function getScoreRatingAttribute()
    {
        if ($this->overall_score >= 80) {
            return 'Good';
        } elseif ($this->overall_score >= 50) {
            return 'Need Improve';
        } else {
            return 'Bad';
        }
    }
}
