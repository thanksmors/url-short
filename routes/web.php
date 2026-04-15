<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'shorten')->name('shorten');
Route::view('/history', 'pages.history-placeholder')->name('history');
Route::view('/about', 'pages.about')->name('about');
