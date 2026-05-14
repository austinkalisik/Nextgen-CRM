<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\DomainHostingRequest;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $openSupportRequests = 0;
        $openDomainRegistrations = 0;

        if ($request->user()?->isStaff()) {
            $openSupportRequests = DomainHostingRequest::query()
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();
            $openDomainRegistrations = DomainHostingRequest::query()
                ->where('service_type', 'domain_registration')
                ->whereNotIn('status', ['completed', 'cancelled'])
                ->count();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'legacy' => [
                'customers' => $request->user()?->isStaff() ? Customer::count() : 0,
                'openSupportRequests' => $openSupportRequests,
                'openDomainRegistrations' => $openDomainRegistrations,
                'notifications' => $openSupportRequests + $openDomainRegistrations,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
