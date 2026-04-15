<?php

use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('rate limit kicks in after 20 creates per minute', function () {
    for ($i = 0; $i < 20; $i++) {
        Volt::test('shorten')
            ->set('url', "https://example.com/{$i}")
            ->call('save');
    }

    Volt::test('shorten')
        ->set('url', 'https://example.com/over')
        ->call('save')
        ->assertHasErrors('url');
});
