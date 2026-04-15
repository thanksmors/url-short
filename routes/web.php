<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'shorten')->name('shorten');
Volt::route('/history', 'history')->name('history');
Route::view('/about', 'pages.about')->name('about');
