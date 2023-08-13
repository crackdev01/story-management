<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoryUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get all stories and users from the database
         $stories = Story::all();
         $users = User::all();
 
         // Attach each user to multiple stories (example: attach 2 random users to each story)
         $stories->each(function ($story, $key) use ($users) {
            // Except for first story attach to first user only
            if ($key === 0) {
                $firstUser = $users->first();
                $story->users()->attach([$firstUser->id]);
            }else{
                $randomUsers = $users->random(2)->pluck('id');
                $story->users()->attach($randomUsers);
            }
         });
    }
}
