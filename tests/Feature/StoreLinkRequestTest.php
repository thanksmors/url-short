<?php

use App\Http\Requests\StoreLinkRequest;
use App\Models\Link;
use Illuminate\Support\Facades\Validator;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function validateLink(array $data): \Illuminate\Contracts\Validation\Validator
{
    $rules = (new StoreLinkRequest())->rules();
    return Validator::make($data, $rules);
}

test('valid url without slug passes', function () {
    expect(validateLink(['url' => 'https://example.com'])->passes())->toBeTrue();
});

test('valid url with slug passes', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'mylink'])->passes())->toBeTrue();
});

test('missing url fails', function () {
    expect(validateLink([])->passes())->toBeFalse();
});

test('invalid url fails', function () {
    expect(validateLink(['url' => 'not-a-url'])->passes())->toBeFalse();
});

test('non-http url fails', function () {
    expect(validateLink(['url' => 'ftp://example.com'])->passes())->toBeFalse();
});

test('slug too short fails', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'abc'])->passes())->toBeFalse();
});

test('slug with bad chars fails', function () {
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'bad slug!'])->passes())->toBeFalse();
});

test('slug collision fails', function () {
    Link::factory()->create(['slug' => 'taken1']);
    expect(validateLink(['url' => 'https://example.com', 'slug' => 'taken1'])->passes())->toBeFalse();
});
