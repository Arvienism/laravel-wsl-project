<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    	DB::table('posts')->insert([
        	['title' => 'First Post', 'body' => 'This is the first seeded post.', 'created_at' => now(), 'updated_at' => now()],
        	['title' => 'Second Post', 'body' => 'This is the second seeded post.', 'created_at' => now(), 'updated_at' => now()],
    	]);
    }
}
