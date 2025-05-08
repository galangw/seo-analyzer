<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ContentAndSeoResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First seed the contents
        $this->call(ContentSeeder::class);
        
        // Then seed the SEO results for each content
        $this->call(SeoResultSeeder::class);
    }
} 