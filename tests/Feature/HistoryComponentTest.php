<?php

use App\Models\Link;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('history renders empty state when no links', function () {
    Volt::test('history')
        ->assertSee('No links yet');
});

test('history lists all links newest first', function () {
    $old = Link::factory()->create(['slug' => 'oldone', 'created_at' => now()->subHour()]);
    $new = Link::factory()->create(['slug' => 'newone', 'created_at' => now()]);

    $component = Volt::test('history');
    $component->assertSee('oldone');
    $component->assertSee('newone');
    $component->assertSeeInOrder(['newone', 'oldone']);
});

test('delete removes the link', function () {
    $link = Link::factory()->create(['slug' => 'tobedel']);

    Volt::test('history')
        ->call('delete', $link->id);

    expect(Link::find($link->id))->toBeNull();
});
