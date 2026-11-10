<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display a paginated listing of published properties with combined filtering.
     */
    public function index(Request $request): View
    {
        $categories = PropertyCategory::orderBy('name')->get();
        $locationConfig = config('nigerian_locations');

        $query = Property::published()
            ->with(['category', 'coverImage'])
            ->latest('published_at');

        // Apply filters
        $query->filter($request->all());

        $properties = $query->paginate(9)->withQueryString();

        return view('public.properties.index', compact('properties', 'categories', 'locationConfig'));
    }

    /**
     * Display the specified published property.
     */
    public function show(string $slug): View
    {
        $query = Property::with(['category', 'images', 'publicDocuments'])
            ->where('slug', $slug);

        // If not logged-in admin, strictly enforce published status
        if (!auth()->check()) {
            $query->published();
        }

        $property = $query->firstOrFail();

        // Related properties in same category
        $relatedProperties = Property::published()
            ->where('id', '!=', $property->id)
            ->where('category_id', $property->category_id)
            ->with(['category', 'coverImage'])
            ->take(3)
            ->get();

        return view('public.properties.show', compact('property', 'relatedProperties'));
    }

    /**
     * Download a public property document.
     */
    public function downloadDocument(string $slug, PropertyDocument $document): StreamedResponse
    {
        $property = Property::where('slug', $slug)->firstOrFail();

        if ($document->property_id !== $property->id) {
            abort(404);
        }

        // Guests cannot download documents unless property is published AND document is marked public
        if (!auth()->check()) {
            if ($property->status !== Property::STATUS_PUBLISHED) {
                abort(404);
            }

            if (!$document->is_public) {
                abort(403, 'This document is private and requires administrator authorization.');
            }
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found on storage.');
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }
}

