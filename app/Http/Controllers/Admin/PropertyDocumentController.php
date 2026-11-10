<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyDocumentController extends Controller
{
    /**
     * Download a private or public property document (Admin only).
     */
    public function download(Property $property, PropertyDocument $document): StreamedResponse
    {
        if ($document->property_id !== $property->id) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document file not found on disk.');
        }

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * Remove a property document.
     */
    public function destroy(Property $property, PropertyDocument $document): RedirectResponse
    {
        if ($document->property_id === $property->id) {
            Storage::disk('local')->delete($document->file_path);
            $document->delete();
        }

        return back()->with('status', 'Property document removed successfully.');
    }
}

