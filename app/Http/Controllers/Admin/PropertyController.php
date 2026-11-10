<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePropertyRequest;
use App\Http\Requests\Admin\UpdatePropertyRequest;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyDocument;
use App\Models\PropertyImage;
use App\Services\PropertyReferenceGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        protected PropertyReferenceGenerator $referenceGenerator
    ) {}

    /**
     * Display a listing of properties.
     */
    public function index(Request $request): View
    {
        $categories = PropertyCategory::orderBy('name')->get();

        $query = Property::with(['category', 'coverImage'])->latest();

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('keyword')) {
            $keyword = '%' . trim($request->input('keyword')) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', $keyword)
                  ->orWhere('reference', 'LIKE', $keyword)
                  ->orWhere('neighbourhood', 'LIKE', $keyword)
                  ->orWhere('state', 'LIKE', $keyword);
            });
        }

        $properties = $query->paginate(15)->withQueryString();

        $statusCounts = Property::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('admin.properties.index', compact('properties', 'categories', 'statusCounts'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create(): View
    {
        $categories = PropertyCategory::orderBy('name')->get();
        $locationConfig = config('nigerian_locations');

        return view('admin.properties.create', compact('categories', 'locationConfig'));
    }

    /**
     * Store a newly created property.
     */
    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $category = PropertyCategory::findOrFail($request->validated('category_id'));

        $reference = $this->referenceGenerator->generate($category);

        $slugBase = Str::slug($request->validated('title'));
        $slug = $slugBase;
        $counter = 1;

        while (Property::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }

        $propertyData = $request->safe()->except(['images', 'cover_index', 'documents']);
        $propertyData['reference'] = $reference;
        $propertyData['slug'] = $slug;
        $propertyData['is_featured'] = $request->boolean('is_featured');
        $propertyData['has_survey'] = $request->boolean('has_survey');

        if ($propertyData['status'] === Property::STATUS_PUBLISHED) {
            $propertyData['published_at'] = now();
        }

        DB::transaction(function () use ($request, $propertyData, &$property) {
            $property = Property::create($propertyData);

            // Handle images
            if ($request->hasFile('images')) {
                $coverIndex = (int) $request->input('cover_index', 0);

                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store("properties/{$property->id}/images", 'public');

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'file_path' => $path,
                        'is_cover' => ($index === $coverIndex),
                        'sort_order' => $index,
                    ]);
                }
            }

            // Handle documents
            if ($request->has('documents')) {
                foreach ($request->file('documents', []) as $docIndex => $docData) {
                    if (isset($docData['file']) && $docData['file']->isValid()) {
                        $file = $docData['file'];
                        $title = $request->input("documents.{$docIndex}.title") ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $type = $request->input("documents.{$docIndex}.type") ?: 'Other';
                        $isPublic = (bool) $request->input("documents.{$docIndex}.is_public", false);

                        $storedPath = $file->store("properties/{$property->id}/documents", 'local');

                        PropertyDocument::create([
                            'property_id' => $property->id,
                            'title' => $title,
                            'document_type' => $type,
                            'file_path' => $storedPath,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                            'is_public' => $isPublic,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.properties.index')->with('status', "Property '{$property->reference}' created successfully.");
    }

    /**
     * Display the specified property.
     */
    public function show(Property $property): View
    {
        $property->load(['category', 'images', 'documents']);

        return view('admin.properties.show', compact('property'));
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit(Property $property): View
    {
        $property->load(['category', 'images', 'documents']);
        $categories = PropertyCategory::orderBy('name')->get();
        $locationConfig = config('nigerian_locations');

        return view('admin.properties.edit', compact('property', 'categories', 'locationConfig'));
    }

    /**
     * Update the specified property.
     */
    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $propertyData = $request->safe()->except(['images', 'cover_image_id', 'delete_images', 'documents', 'delete_documents']);
        $propertyData['is_featured'] = $request->boolean('is_featured');
        $propertyData['has_survey'] = $request->boolean('has_survey');

        if ($propertyData['status'] === Property::STATUS_PUBLISHED && !$property->published_at) {
            $propertyData['published_at'] = now();
        }

        DB::transaction(function () use ($request, $property, $propertyData) {
            $property->update($propertyData);

            // Delete requested images
            if ($request->has('delete_images')) {
                $imagesToDelete = PropertyImage::where('property_id', $property->id)
                    ->whereIn('id', $request->input('delete_images'))
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->file_path);
                    $image->delete();
                }
            }

            // Set cover image
            if ($request->filled('cover_image_id')) {
                PropertyImage::where('property_id', $property->id)->update(['is_cover' => false]);
                PropertyImage::where('property_id', $property->id)
                    ->where('id', $request->input('cover_image_id'))
                    ->update(['is_cover' => true]);
            }

            // Upload new images
            if ($request->hasFile('images')) {
                $existingCount = PropertyImage::where('property_id', $property->id)->count();
                $hasCover = PropertyImage::where('property_id', $property->id)->where('is_cover', true)->exists();

                foreach ($request->file('images') as $index => $imageFile) {
                    $path = $imageFile->store("properties/{$property->id}/images", 'public');
                    $isCover = (!$hasCover && $index === 0);

                    PropertyImage::create([
                        'property_id' => $property->id,
                        'file_path' => $path,
                        'is_cover' => $isCover,
                        'sort_order' => $existingCount + $index,
                    ]);

                    if ($isCover) {
                        $hasCover = true;
                    }
                }
            }

            // Delete requested documents
            if ($request->has('delete_documents')) {
                $docsToDelete = PropertyDocument::where('property_id', $property->id)
                    ->whereIn('id', $request->input('delete_documents'))
                    ->get();

                foreach ($docsToDelete as $doc) {
                    Storage::disk('local')->delete($doc->file_path);
                    $doc->delete();
                }
            }

            // Upload new documents
            if ($request->has('documents')) {
                foreach ($request->file('documents', []) as $docIndex => $docData) {
                    if (isset($docData['file']) && $docData['file']->isValid()) {
                        $file = $docData['file'];
                        $title = $request->input("documents.{$docIndex}.title") ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $type = $request->input("documents.{$docIndex}.type") ?: 'Other';
                        $isPublic = (bool) $request->input("documents.{$docIndex}.is_public", false);

                        $storedPath = $file->store("properties/{$property->id}/documents", 'local');

                        PropertyDocument::create([
                            'property_id' => $property->id,
                            'title' => $title,
                            'document_type' => $type,
                            'file_path' => $storedPath,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                            'is_public' => $isPublic,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.properties.index')->with('status', "Property '{$property->reference}' updated successfully.");
    }

    /**
     * Fast update for status toggle.
     */
    public function updateStatus(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Property::STATUSES))],
        ]);

        $status = $validated['status'];
        $publishedAt = ($status === Property::STATUS_PUBLISHED && !$property->published_at) ? now() : $property->published_at;

        $property->update([
            'status' => $status,
            'published_at' => $publishedAt,
        ]);

        return back()->with('status', "Status for '{$property->reference}' changed to " . Property::STATUSES[$status] . ".");
    }

    /**
     * Delete an image.
     */
    public function deleteImage(Property $property, PropertyImage $image): RedirectResponse
    {
        if ($image->property_id === $property->id) {
            Storage::disk('public')->delete($image->file_path);
            $image->delete();
        }

        return back()->with('status', 'Property image removed.');
    }

    /**
     * Set cover image.
     */
    public function setCoverImage(Property $property, PropertyImage $image): RedirectResponse
    {
        if ($image->property_id === $property->id) {
            PropertyImage::where('property_id', $property->id)->update(['is_cover' => false]);
            $image->update(['is_cover' => true]);
        }

        return back()->with('status', 'Cover image updated.');
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(Property $property): RedirectResponse
    {
        DB::transaction(function () use ($property) {
            // Delete image files
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->file_path);
            }

            // Delete document files
            foreach ($property->documents as $document) {
                Storage::disk('local')->delete($document->file_path);
            }

            $property->delete();
        });

        return redirect()->route('admin.properties.index')->with('status', "Property '{$property->reference}' deleted successfully.");
    }
}
