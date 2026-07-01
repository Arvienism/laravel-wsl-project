<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Todo;
use Illuminate\Support\Carbon;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        Todo::create([
            'title' => 'Learn Livewire',
            'status' => 'pending',
            'priority' => true,
            'due_at' => Carbon::now()->addHours(6),
        ]);

        Todo::create([
            'title' => 'Finish OJT report',
            'completed' => true,
            'status' => 'completed',
            'due_at' => Carbon::now()->subDay(),
        ]);

        Todo::create([
            'title' => 'Study Laravel',
            'status' => 'not_yet',
            'priority' => false,
            'due_at' => Carbon::now()->addDay(),
        ]);
    }
}