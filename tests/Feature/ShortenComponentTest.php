<?php

use App\Models\Link;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('submitting valid url creates a link and redirects', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->call('save')
        ->assertRedirect('/history');

    expect(Link::count())->toBe(1);
    expect(Link::first()->original_url)->toBe('https://example.com');
    expect(Link::first()->slug)->toHaveLength(6);
});

test('submitting url with custom slug uses that slug', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->set('slug', 'mylink')
        ->call('save')
        ->assertRedirect('/history');

    expect(Link::where('slug', 'mylink')->exists())->toBeTrue();
});

test('invalid url shows validation error and does not create link', function () {
    Volt::test('shorten')
        ->set('url', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['url']);

    expect(Link::count())->toBe(0);
});

test('slug collision shows validation error', function () {
    Link::factory()->create(['slug' => 'taken1']);

    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->set('slug', 'taken1')
        ->call('save')
        ->assertHasErrors(['slug']);

    expect(Link::count())->toBe(1);
});

test('empty list initially — create sets flash message', function () {
    Volt::test('shorten')
        ->set('url', 'https://example.com')
        ->call('save');

    expect(session('flash'))->toContain('Link created');
});
