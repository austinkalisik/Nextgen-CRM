import { Head, useForm } from '@inertiajs/react';
import { Star } from 'lucide-react';
import type { FormEvent } from 'react';

const emptyCustomer = {
    company_name: '',
    contact_name: '',
    email: '',
    phone: '',
    industry: 'Hosting',
    status: 'active',
    website: '',
    address: '',
    notes: '',
    next_follow_up_at: '',
};

export default function AddCustomer() {
    const form = useForm({ ...emptyCustomer });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.post('/customers', { preserveScroll: true });
    };

    return (
        <>
            <Head title="Add New Customer" />
            <form onSubmit={submit} className="legacy-panel legacy-form-panel">
                <div className="legacy-panel-title">
                    <span>Customer and Plan Details</span>
                    <button
                        type="submit"
                        className="legacy-save"
                        disabled={form.processing}
                    >
                        <Star size={14} /> Save Customer
                    </button>
                </div>
                <div className="legacy-panel-body">
                    <div className="legacy-form-grid">
                        <section>
                            <h2>Contact &amp; Account Details</h2>
                            <Field label="Customer Name">
                                <input
                                    value={form.data.company_name}
                                    onChange={(e) =>
                                        form.setData(
                                            'company_name',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Customer Name"
                                    required
                                />
                            </Field>
                            <Field label="Contact Name">
                                <input
                                    value={form.data.contact_name}
                                    onChange={(e) =>
                                        form.setData(
                                            'contact_name',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Contact Name"
                                    required
                                />
                            </Field>
                            <Field label="Address">
                                <textarea
                                    value={form.data.address}
                                    onChange={(e) =>
                                        form.setData('address', e.target.value)
                                    }
                                    placeholder=""
                                />
                            </Field>
                            <Field label="Phone">
                                <input
                                    value={form.data.phone}
                                    onChange={(e) =>
                                        form.setData('phone', e.target.value)
                                    }
                                    placeholder="Phone Number"
                                />
                            </Field>
                            <Field label="Email Address">
                                <input
                                    type="email"
                                    value={form.data.email}
                                    onChange={(e) =>
                                        form.setData('email', e.target.value)
                                    }
                                    placeholder="Email Address"
                                    required
                                />
                            </Field>
                        </section>

                        <section>
                            <h2>Plan Details</h2>
                            <div className="legacy-customer-type">
                                <Field label="Customer Type">
                                    <label>
                                        <input type="checkbox" defaultChecked />{' '}
                                        Current Customer
                                    </label>
                                    <label>
                                        <input type="checkbox" /> Complimentary
                                    </label>
                                </Field>
                                <Field label="OWA Customer">
                                    <label>
                                        <input name="owa" type="radio" /> Yes
                                    </label>
                                    <label>
                                        <input
                                            name="owa"
                                            type="radio"
                                            defaultChecked
                                        />{' '}
                                        No
                                    </label>
                                </Field>
                                <Field label="OWA-A Customer">
                                    <label>
                                        <input name="owaa" type="radio" /> Yes
                                    </label>
                                    <label>
                                        <input
                                            name="owaa"
                                            type="radio"
                                            defaultChecked
                                        />{' '}
                                        No
                                    </label>
                                </Field>
                            </div>
                            <Field label="Hosting Plan">
                                <select
                                    value={form.data.industry}
                                    onChange={(e) =>
                                        form.setData('industry', e.target.value)
                                    }
                                >
                                    <option>Mail only</option>
                                    <option>Hosting</option>
                                    <option>Domain</option>
                                    <option>Premium</option>
                                </select>
                            </Field>
                            <Field label="Start Date">
                                <input
                                    type="text"
                                    placeholder="Hosting Start Date"
                                />
                            </Field>
                            <Field label="Renewal Date">
                                <input
                                    type="date"
                                    value={form.data.next_follow_up_at}
                                    onChange={(e) =>
                                        form.setData(
                                            'next_follow_up_at',
                                            e.target.value,
                                        )
                                    }
                                />
                            </Field>
                            <Field label="Invoice Cycle">
                                <select>
                                    <option>Yearly</option>
                                    <option>Monthly</option>
                                </select>
                            </Field>
                            <Field label="Spam Filter Blocks">
                                <input placeholder="Spam Filter Blocks" />
                            </Field>
                            <Field label="Spam Filter Emails">
                                <input placeholder="Spam Filter Mailboxes" />
                            </Field>
                        </section>
                    </div>

                    <section className="legacy-host-row">
                        <h2>Host Location Details</h2>
                        <div>
                            <Field label="Host Location">
                                <input placeholder="Host Location" />
                            </Field>
                            <Field label="CP Username">
                                <input placeholder="CP Username" />
                            </Field>
                            <Field label="CP Password">
                                <input placeholder="CP Password" />
                            </Field>
                        </div>
                    </section>
                </div>
            </form>
        </>
    );
}

function Field({
    label,
    children,
}: {
    label: string;
    children: React.ReactNode;
}) {
    return (
        <label className="legacy-field">
            <span>{label}</span>
            {children}
        </label>
    );
}

AddCustomer.layout = {
    breadcrumbs: [{ title: 'Add New Customer', href: '/add-customer' }],
};
