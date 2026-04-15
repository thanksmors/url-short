<?php

use App\Models\Link;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('link uses slug as route key', function () {
    $link = new Link();
    expect($link->getRouteKeyName())->toBe('slug');
});

test('link can be created with fillable attributes', function () {
    $link = Link::create([
        'slug' => 'abc123',
        'original_url' => 'https://example.com',
    ]);

    expect($link->slug)->toBe('abc123');
    expect($link->original_url)->toBe('https://example.com');
});

test('link factory produces valid links', function () {
    $link = Link::factory()->create();
    expect($link->slug)->toBeString();
    expect($link->original_url)->toBeString();
});

test('slug must be unique', function () {
    Link::factory()->create(['slug' => 'taken1']);
    expect(fn () => Link::factory()->create(['slug' => 'taken1']))
        ->toThrow(\Illuminate\Database\QueryException::class);
});
