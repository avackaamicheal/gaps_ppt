<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage with dynamic featured properties and client testimonials.
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

        $testimonials = Testimonial::published()
            ->with('property:id,title,reference')
            ->take(6)
            ->get();

        return view('public.home', compact('featuredProperties', 'categories', 'testimonials'));
    }
}
