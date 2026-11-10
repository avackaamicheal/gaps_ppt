<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Support\Facades\DB;

class PropertyReferenceGenerator
{
    /**
     * Generate a unique, concurrent-safe reference for a property.
     *
     * Example outputs: LAND-0001, PROP-0001, COMM-0001
     */
    public function generate(PropertyCategory $category): string
    {
        $prefix = strtoupper($category->code_prefix ?: 'PROP');

        return DB::transaction(function () use ($prefix) {
            // Find the most recent reference for this prefix using pessimistic lock
            $latestRef = Property::withTrashed()
                ->where('reference', 'LIKE', "{$prefix}-%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('reference');

            $nextNumber = 1;

            if ($latestRef && preg_match('/^' . preg_quote($prefix, '/') . '-(\d+)$/', $latestRef, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }

            $reference = sprintf('%s-%04d', $prefix, $nextNumber);

            // Double check existence in case of legacy/unsequenced records
            while (Property::withTrashed()->where('reference', $reference)->exists()) {
                $nextNumber++;
                $reference = sprintf('%s-%04d', $prefix, $nextNumber);
            }

            return $reference;
        });
    }
}
