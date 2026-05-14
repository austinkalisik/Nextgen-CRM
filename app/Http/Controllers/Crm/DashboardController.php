<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DomainHostingRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
                    'domains' => DomainHostingRequest::count(),
                    'suspendedCustomers' => Customer::where('status', 'suspended')->count(),
                    'supportRequests' => DomainHostingRequest::whereNotIn('status', ['completed', 'cancelled'])->count(),
                    'domainRegistrations' => DomainHostingRequest::where('service_type', 'domain_registration')->count(),
                ],
                'customers' => Customer::withCount('domainHostingRequests')
                    ->latest()
                    ->limit(25)
                    ->get(),
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

    public function renewals(Request $request): Response
    {
        abort_unless($request->user()->isStaff(), 403);

        $month = $request->string('month', now()->format('Y-m'))->toString();

        try {
            $date = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            $date = now()->startOfMonth();
        }

        return Inertia::render('renewals/index', [
            'renewals' => DomainHostingRequest::with('customer:id,company_name')
                ->whereNotNull('renewal_date')
                ->whereBetween('renewal_date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->orderBy('renewal_date')
                ->get()
                ->map(fn (DomainHostingRequest $request) => [
                    'id' => $request->id,
                    'customer_name' => $request->customer?->company_name ?? 'Unknown Customer',
                    'current_renewal_date' => optional($request->renewal_date)->toDateString(),
                    'next_renewal_date' => optional($request->renewal_date?->copy()->addYear())->toDateString(),
                    'renewal_cycle' => 'Yearly',
                    'renewal_item' => str_contains($request->service_type, 'hosting') ? 'Hosting' : 'Domain',
                ]),
            'month' => $date->format('Y-m'),
            'monthLabel' => $date->format('F Y'),
        ]);
    }

    public function updateRenewals(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isStaff(), 403);

        $data = $request->validate([
            'renewals' => ['required', 'array'],
            'renewals.*.id' => ['required', 'integer', 'exists:domain_hosting_requests,id'],
            'renewals.*.selected' => ['boolean'],
            'renewals.*.next_renewal_date' => ['required', 'date'],
        ]);

        $updated = 0;

        foreach ($data['renewals'] as $renewal) {
            if (! ($renewal['selected'] ?? false)) {
                continue;
            }

            DomainHostingRequest::whereKey($renewal['id'])->update([
                'renewal_date' => $renewal['next_renewal_date'],
            ]);
            $updated++;
        }

        return back()->with('success', "{$updated} renewal record(s) updated.");
    }

    public function bulkEmailValidator(Request $request): Response
    {
        abort_unless($request->user()->isStaff(), 403);

        return Inertia::render('tools/bulk-email-validator', [
            'results' => session('email_validation_results', []),
            'input' => session('email_validation_input', ''),
        ]);
    }

    public function validateEmails(Request $request)
    {
        abort_unless($request->user()->isStaff(), 403);

        $input = $request->string('emails')->toString();
        $results = collect(preg_split('/\R+/', $input) ?: [])
            ->map(fn (string $email) => trim($email))
            ->filter()
            ->map(fn (string $email) => [
                'email' => $email,
                'valid' => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
            ])
            ->values()
            ->all();

        return back()
            ->with('email_validation_results', $results)
            ->with('email_validation_input', $input);
    }
}
