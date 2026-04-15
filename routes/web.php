<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.shorten-placeholder')->name('shorten');
Route::view('/history', 'pages.history-placeholder')->name('history');
Route::view('/about', 'pages.about')->name('about');
