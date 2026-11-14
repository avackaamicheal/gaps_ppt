<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $properties = Property::published()
            ->latest('updated_at')
            ->get(['slug', 'updated_at']);

        $xml = view('public.sitemap', compact('properties'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}

