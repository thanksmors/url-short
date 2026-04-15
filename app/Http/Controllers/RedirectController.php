<?php

namespace App\Http\Controllers;

use App\Models\Link;

class RedirectController extends Controller
{
    public function __invoke(string $slug)
    {
        $link = Link::where('slug', $slug)->first();

        if (! $link) {
            abort(404);
        }

        return redirect()->away($link->original_url, 302);
    }
}
