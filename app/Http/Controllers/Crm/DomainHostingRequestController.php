<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DomainHostingRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DomainHostingRequestController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isStaff(), 403);

        return Inertia::render('hosting-requests/index', [
            'requests' => DomainHostingRequest::with(['customer:id,company_name', 'assignee:id,name'])
                ->latest()
                ->get(),
            'customers' => Customer::orderBy('company_name')->get(['id', 'company_name']),
            'staff' => User::whereIn('role', ['admin', 'staff'])->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (! $request->user()->isStaff()) {
            abort_if($request->user()->customer_id !== (int) $data['customer_id'], 403);
            $data['status'] = 'new';
            $data['assigned_to'] = null;
            $data['quoted_amount'] = null;
            $data['internal_notes'] = null;
        }

        DomainHostingRequest::create($data);

        return back()->with('success', 'Domain hosting request saved.');
    }

    public function update(Request $request, DomainHostingRequest $hostingRequest): RedirectResponse
    {
        abort_unless($request->user()->isStaff(), 403);

        $hostingRequest->update($this->validated($request));

        return back()->with('success', 'Domain hosting request updated.');
    }

    public function destroy(Request $request, DomainHostingRequest $hostingRequest): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $hostingRequest->delete();

        return back()->with('success', 'Domain hosting request deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'domain_name' => ['required', 'string', 'max:255'],
            'service_type' => ['required', Rule::in(['domain_registration', 'website_hosting', 'email_hosting', 'email_antispam', 'domain_hosting', 'ssl', 'domain_transfer', 'isp_connectivity', 'network_infrastructure', 'cctv_security', 'document_management', 'vehicle_tracking', 'audio_visual', 'web_app_development', 'support_contract'])],
            'plan' => ['required', Rule::in(['basic', 'standard', 'value', 'premium', 'starter', 'business', 'enterprise', 'custom'])],
            'status' => ['required', Rule::in(['new', 'reviewing', 'quoted', 'approved', 'provisioning', 'completed', 'cancelled'])],
            'requested_start_date' => ['nullable', 'date'],
            'renewal_date' => ['nullable', 'date'],
            'quoted_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
