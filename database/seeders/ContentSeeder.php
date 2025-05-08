<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a user or create one if none exists
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create 30 content entries spread across May 2-7, 2025
        $startDate = Carbon::createFromDate(2025, 5, 2);
        $endDate = Carbon::createFromDate(2025, 5, 3);

        // Calculate how many days in the range
        $dayCount = $endDate->diffInDays($startDate) + 1; // +1 to include end date
        
        // We'll need at least 30 entries, so calculate how many per day (minimum 5 per day)
        $entriesPerDay = max(5, ceil(30 / $dayCount)); 
        
        // Keep track of total
        $totalEntries = 0;
        
        // Loop through each day
        for ($day = 0; $day < $dayCount; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            
            // For each day, create multiple entries with timestamps within that day
            for ($i = 0; $i < $entriesPerDay; $i++) {
                // Generate a random time within the day
                $hour = rand(8, 22); // between 8 AM and 10 PM
                $minute = rand(0, 59);
                $second = rand(0, 59);
                
                $timestamp = $currentDate->copy()->setTime($hour, $minute, $second);
                
                Content::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
                
                $totalEntries++;
                
                // If we've reached 30 entries and it's not the first day, we can stop
                if ($totalEntries >= 30 && $day > 0) {
                    break 2; // Break both loops
                }
            }
        }
        
        // If we somehow have less than 30 entries, add more on the last day
        if ($totalEntries < 30) {
            $neededEntries = 30 - $totalEntries;
            for ($i = 0; $i < $neededEntries; $i++) {
                $hour = rand(8, 22);
                $minute = rand(0, 59);
                $second = rand(0, 59);
                
                $timestamp = $endDate->copy()->setTime($hour, $minute, $second);
                
                Content::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }
    }
} 