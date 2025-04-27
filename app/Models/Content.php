<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'meta_description',
        'content',
        'target_keyword',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seoResults()
    {
        return $this->hasMany(SeoResult::class);
    }

    public function latestSeoResult()
    {
        return $this->hasOne(SeoResult::class)->latest();
    }
}
