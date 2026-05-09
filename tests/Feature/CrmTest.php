<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DomainHostingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_customer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/customers', [
                'company_name' => 'Acme PNG',
                'contact_name' => 'Ada Customer',
                'email' => 'ada@example.com',
                'phone' => '+675 1234 5678',
                'industry' => 'Retail',
                'status' => 'active',
                'website' => 'https://example.com',
                'address' => 'Port Moresby',
                'notes' => 'Priority customer',
                'next_follow_up_at' => now()->addDay()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'company_name' => 'Acme PNG',
            'email' => 'ada@example.com',
            'status' => 'active',
        ]);
    }

    public function test_customer_can_create_own_hosting_request(): void
    {
        $customer = Customer::create([
            'company_name' => 'Client PNG',
            'contact_name' => 'Client User',
            'email' => 'client@example.com',
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'role' => 'customer',
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($user)
            ->post('/hosting-requests', [
                'customer_id' => $customer->id,
                'domain_name' => 'clientpng.com',
                'service_type' => 'domain_hosting',
                'plan' => 'value',
                'status' => 'approved',
                'requested_start_date' => now()->toDateString(),
                'requirements' => 'Hosting and email setup',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('domain_hosting_requests', [
            'customer_id' => $customer->id,
            'domain_name' => 'clientpng.com',
            'status' => 'new',
            'assigned_to' => null,
        ]);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_can_create_customer_login_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'company_name' => 'Portal Client',
            'contact_name' => 'Portal Contact',
            'email' => 'portal@example.com',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post('/users', [
                'name' => 'Portal Login',
                'email' => 'login@example.com',
                'phone' => '+675 7000 0000',
                'role' => 'customer',
                'customer_id' => $customer->id,
                'is_active' => true,
                'password' => 'password123',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'login@example.com',
            'role' => 'customer',
            'customer_id' => $customer->id,
        ]);
    }

    public function test_admin_can_update_hosting_request(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create([
            'company_name' => 'Hosting Client',
            'contact_name' => 'Hosting Contact',
            'email' => 'hosting@example.com',
            'status' => 'active',
        ]);
        $request = DomainHostingRequest::create([
            'customer_id' => $customer->id,
            'domain_name' => 'old-domain.com',
            'service_type' => 'website_hosting',
            'plan' => 'starter',
            'status' => 'new',
        ]);

        $this->actingAs($admin)
            ->put("/hosting-requests/{$request->id}", [
                'customer_id' => $customer->id,
                'domain_name' => 'new-domain.com',
                'service_type' => 'domain_hosting',
                'plan' => 'enterprise',
                'status' => 'completed',
                'quoted_amount' => 2500,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('domain_hosting_requests', [
            'id' => $request->id,
            'domain_name' => 'new-domain.com',
            'status' => 'completed',
            'quoted_amount' => 2500,
        ]);
    }
}
