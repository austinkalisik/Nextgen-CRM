<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\DomainHostingRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $nextgen = Customer::create([
            'company_name' => 'NextGen PNG',
            'contact_name' => 'Customer Contact',
            'email' => 'customer@nextgenpng.net',
            'phone' => '+675 0000 0000',
            'industry' => 'ICT Services',
            'status' => 'active',
            'website' => 'https://nextgenpng.net/',
            'address' => 'Port Moresby, Papua New Guinea',
            'notes' => 'Demo customer record for customer portal testing.',
            'next_follow_up_at' => now()->addWeek()->toDateString(),
        ]);

        $admin = User::factory()->create([
            'name' => 'NextGen Admin',
            'email' => 'admin@nextgenpng.net',
            'role' => 'admin',
            'phone' => '+675 7000 0001',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'NextGen Staff',
            'email' => 'staff@nextgenpng.net',
            'role' => 'staff',
            'phone' => '+675 7000 0002',
            'is_active' => true,
        ]);

        User::factory()->create([
            'name' => 'Customer Login',
            'email' => 'customer@nextgenpng.net',
            'role' => 'customer',
            'customer_id' => $nextgen->id,
            'phone' => '+675 7000 0003',
            'is_active' => true,
        ]);

        DomainHostingRequest::create([
            'customer_id' => $nextgen->id,
            'assigned_to' => $admin->id,
            'domain_name' => 'nextgenpng.net',
            'service_type' => 'domain_hosting',
            'plan' => 'value',
            'status' => 'provisioning',
            'requested_start_date' => now()->toDateString(),
            'renewal_date' => now()->addYear()->toDateString(),
            'quoted_amount' => 1200,
            'requirements' => 'Domain hosting, SSL, business email, and renewal tracking.',
            'internal_notes' => 'Seeded CRM request for dashboard and customer portal testing.',
        ]);
    }
}
