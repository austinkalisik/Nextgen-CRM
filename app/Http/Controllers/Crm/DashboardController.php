<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DomainHostingRequest;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $user = request()->user();

        if ($user->isStaff()) {
            return Inertia::render('dashboard', [
                'metrics' => [
                    'customers' => Customer::count(),
                    'activeCustomers' => Customer::where('status', 'active')->count(),
                    'openRequests' => DomainHostingRequest::whereNotIn('status', ['completed', 'cancelled'])->count(),
                    'users' => User::count(),
                ],
                'recentRequests' => DomainHostingRequest::with('customer:id,company_name')
                    ->latest()
                    ->limit(8)
                    ->get(),
            ]);
        }

        $customerId = $user->customer_id;

        return Inertia::render('portal/index', [
            'customer' => $user->customer,
            'requests' => DomainHostingRequest::where('customer_id', $customerId)
                ->latest()
                ->get(),
        ]);
    }
}
