<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('shorten page loads', function () {
    $this->get('/')->assertOk()->assertSee('Shorten');
});

test('history page loads', function () {
    $this->get('/history')->assertOk()->assertSee('History');
});

test('about page loads', function () {
    $this->get('/about')->assertOk()->assertSee('About Shorty');
});
