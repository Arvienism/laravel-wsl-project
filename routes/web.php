<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/todo', 'pages.todo.index')
    ->name('todo.index');