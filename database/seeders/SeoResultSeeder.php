<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\SeoResult;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SeoResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all content entries
        $contents = Content::all();

        foreach ($contents as $content) {
            // Create an SEO result for each content with a timestamp shortly after content creation
            $contentCreatedAt = Carbon::parse($content->created_at);
            
            // Add a random number of minutes (5-60) to simulate analysis time
            $resultTimestamp = $contentCreatedAt->copy()->addMinutes(rand(5, 60));
            
            SeoResult::factory()->create([
                'content_id' => $content->id,
                'created_at' => $resultTimestamp,
                'updated_at' => $resultTimestamp
            ]);
        }
    }
} 