<?php

use App\Models\Link;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('existing slug redirects 302 to original url', function () {
    Link::factory()->create([
        'slug' => 'abc123',
        'original_url' => 'https://example.com/page',
    ]);

    $response = $this->get('/r/abc123');
    $response->assertStatus(302);
    $response->assertRedirect('https://example.com/page');
});

test('missing slug returns 404 with link-not-found view', function () {
    $this->get('/r/nosuch')
        ->assertStatus(404)
        ->assertSee('Link not found');
});
