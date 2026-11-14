<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTestimonialRequest;
use App\Http\Requests\Admin\UpdateTestimonialRequest;
use App\Models\Property;
use App\Models\Testimonial;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = Testimonial::with('property:id,title,reference');

        if ($status === 'published') {
            $query->where('is_published', true);
        } elseif ($status === 'unpublished') {
            $query->where('is_published', false);
        }

        $testimonials = $query->orderBy('sort_order', 'asc')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total' => Testimonial::count(),
            'published' => Testimonial::where('is_published', true)->count(),
            'unpublished' => Testimonial::where('is_published', false)->count(),
        ];

        return view('admin.testimonials.index', compact('testimonials', 'counts', 'status'));
    }

    public function create(): View
    {
        $properties = Property::orderBy('title')->get(['id', 'title', 'reference']);

        return view('admin.testimonials.create', compact('properties'));
    }

    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $testimonial = Testimonial::create($data);

        ActivityLogger::log(
            'testimonial.created',
            "Recorded client testimonial from {$testimonial->client_name} ({$testimonial->rating}★)",
            $testimonial
        );

        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimonial from {$testimonial->client_name} created successfully.");
    }

    public function edit(Testimonial $testimonial): View
    {
        $properties = Property::orderBy('title')->get(['id', 'title', 'reference']);

        return view('admin.testimonials.edit', compact('testimonial', 'properties'));
    }

    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $testimonial->update($data);

        ActivityLogger::log(
            'testimonial.updated',
            "Updated client testimonial for {$testimonial->client_name}",
            $testimonial
        );

        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimonial from {$testimonial->client_name} updated successfully.");
    }

    public function togglePublish(Testimonial $testimonial): RedirectResponse
    {
        $newStatus = !$testimonial->is_published;
        $testimonial->update(['is_published' => $newStatus]);

        ActivityLogger::log(
            'testimonial.toggled_publish',
            ($newStatus ? 'Published' : 'Unpublished') . " testimonial from {$testimonial->client_name}",
            $testimonial
        );

        return back()->with('success', "Testimonial is now " . ($newStatus ? 'published' : 'hidden from public view') . ".");
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $clientName = $testimonial->client_name;

        ActivityLogger::log(
            'testimonial.deleted',
            "Deleted client testimonial from {$clientName}"
        );

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', "Testimonial from {$clientName} has been removed.");
    }
}

