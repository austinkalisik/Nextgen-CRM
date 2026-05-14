<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('users/index', [
            'users' => User::with('customer:id,company_name')->latest()->get(),
            'customers' => Customer::orderBy('company_name')->get(['id', 'company_name']),
        ]);
    }

    public function legacySettings(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('admin-settings/index', [
            'users' => User::whereIn('role', ['admin', 'staff'])->latest()->get(),
            'settings' => [
                'email_from_address' => SystemSetting::getValue('email_from_address', config('mail.from.address', 'support@nextgenpng.net')),
                'email_from_name' => SystemSetting::getValue('email_from_name', config('mail.from.name', 'NextGen Support')),
                'mail_host' => SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host', 'mail.nextgenpng.net')),
                'mail_port' => SystemSetting::getValue('mail_port', (string) config('mail.mailers.smtp.port', 25)),
                'send_email_user_ids' => json_decode(SystemSetting::getValue('send_email_user_ids', '[]') ?? '[]', true),
            ],
        ]);
    }

    public function updateLegacySettings(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'email_from_address' => ['required', 'email', 'max:255'],
            'email_from_name' => ['required', 'string', 'max:255'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'send_email_user_ids' => ['array'],
            'send_email_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        foreach (['email_from_address', 'email_from_name', 'mail_host', 'mail_port'] as $key) {
            SystemSetting::setValue($key, (string) $data[$key]);
        }

        SystemSetting::setValue('send_email_user_ids', json_encode($data['send_email_user_ids'] ?? []));

        return back()->with('success', 'Admin settings saved.');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        User::create($this->validated($request) + ['password' => $request->string('password')->toString()]);

        return back()->with('success', 'User created.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $this->validated($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($request->user()->is($user), 422, 'You cannot delete your own account.');

        $user->delete();

        return back()->with('success', 'User deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::in(['admin', 'staff', 'customer'])],
            'customer_id' => ['nullable', 'required_if:role,customer', 'exists:customers,id'],
            'is_active' => ['boolean'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
        ]);
    }
}
