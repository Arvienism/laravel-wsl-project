<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Todo;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        Todo::create(['title' => 'Learn Livewire']);
        Todo::create(['title' => 'Finish OJT report', 'completed' => true]);
        Todo::create(['title' => 'Study Laravel']);
    }
}