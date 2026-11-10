<?php

namespace App\Services;

use App\Models\Customer;

class CustomerManager
{
    /**
     * Find an existing customer by normalized phone or email, or create a new one.
     */
    public function findOrCreateCustomer(array $data): Customer
    {
        $rawPhone = $data['phone'] ?? '';
        $normalizedPhone = Customer::normalizePhone($rawPhone);
        $localPhone = '';
        if (str_starts_with($normalizedPhone, '234') && strlen($normalizedPhone) === 13) {
            $localPhone = '0' . substr($normalizedPhone, 3);
        }

        $email = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
        $name = trim($data['name'] ?? 'Prospective Buyer');
        $source = $data['source'] ?? Customer::SOURCE_WEBSITE;

        // Try to match by raw phone, normalized phone, local phone variant, or email
        $customer = Customer::where(function ($query) use ($rawPhone, $normalizedPhone, $localPhone, $email) {
            $query->where('phone', $rawPhone);

            if (!empty($normalizedPhone)) {
                $query->orWhere('phone', $normalizedPhone);
            }

            if (!empty($localPhone)) {
                $query->orWhere('phone', $localPhone);
            }

            if (!empty($email)) {
                $query->orWhere('email', $email);
            }
        })->first();

        if ($customer) {
            // Update email if previously empty and now provided
            if (empty($customer->email) && !empty($email)) {
                $customer->email = $email;
                $customer->save();
            }

            return $customer;
        }

        // Create new customer
        return Customer::create([
            'name' => $name,
            'phone' => $rawPhone,
            'email' => $email,
            'source' => $source,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
