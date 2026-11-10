<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage with dynamic featured properties.
     */
    public function index(): View
    {
        $featuredProperties = Property::published()
            ->with(['category', 'coverImage'])
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(6)
            ->get();

        if ($featuredProperties->isEmpty()) {
            $featuredProperties = Property::published()
                ->with(['category', 'coverImage'])
                ->latest('published_at')
                ->take(6)
                ->get();
        }

        $categories = PropertyCategory::withCount(['properties' => function ($query) {
            $query->published();
        }])->get();

        return view('public.home', compact('featuredProperties', 'categories'));
    }
}
