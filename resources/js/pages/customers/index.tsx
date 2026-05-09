import { Head, router, useForm } from '@inertiajs/react';
import { Save, Trash2 } from 'lucide-react';
import { FormEvent, useMemo, useState } from 'react';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Customer } from '@/types';

type Props = {
    customers: Customer[];
};

const emptyCustomer = {
    company_name: '',
    contact_name: '',
    email: '',
    phone: '',
    industry: '',
    status: 'lead',
    website: '',
    address: '',
    notes: '',
    next_follow_up_at: '',
};

export default function CustomersIndex({ customers }: Props) {
    const [editing, setEditing] = useState<Customer | null>(null);
    const form = useForm({ ...emptyCustomer });

    const title = useMemo(() => (editing ? 'Edit Customer' : 'New Customer'), [editing]);

    const edit = (customer: Customer) => {
        setEditing(customer);
        form.setData({
            company_name: customer.company_name,
            contact_name: customer.contact_name,
            email: customer.email,
            phone: customer.phone ?? '',
            industry: customer.industry ?? '',
            status: customer.status,
            website: customer.website ?? '',
            address: customer.address ?? '',
            notes: customer.notes ?? '',
            next_follow_up_at: customer.next_follow_up_at ?? '',
        });
    };

    const reset = () => {
        setEditing(null);
        form.setData({ ...emptyCustomer });
        form.clearErrors();
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = { preserveScroll: true, onSuccess: reset };

        if (editing) {
            form.put(`/customers/${editing.id}`, options);
            return;
        }

        form.post('/customers', options);
    };

    return (
        <>
            <Head title="Customers" />
            <div className="grid gap-4 p-4 xl:grid-cols-[1fr_420px]">
                <Card>
                    <CardHeader>
                        <CardTitle>Customer Information</CardTitle>
                        <CardDescription>Company profile, contacts, status, and follow-up tracking.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b text-left text-muted-foreground">
                                        <th className="py-2 font-medium">Company</th>
                                        <th className="py-2 font-medium">Contact</th>
                                        <th className="py-2 font-medium">Status</th>
                                        <th className="py-2 font-medium">Requests</th>
                                        <th className="py-2 text-right font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {customers.map((customer) => (
                                        <tr key={customer.id} className="border-b last:border-0">
                                            <td className="py-3">
                                                <div className="font-medium">{customer.company_name}</div>
                                                <div className="text-muted-foreground">{customer.email}</div>
                                            </td>
                                            <td className="py-3">
                                                <div>{customer.contact_name}</div>
                                                <div className="text-muted-foreground">{customer.phone || 'No phone'}</div>
                                            </td>
                                            <td className="py-3">
                                                <Badge variant={customer.status === 'active' ? 'default' : 'secondary'}>
                                                    {customer.status}
                                                </Badge>
                                            </td>
                                            <td className="py-3">{customer.domain_hosting_requests_count ?? 0}</td>
                                            <td className="py-3 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Button size="sm" variant="outline" onClick={() => edit(customer)}>
                                                        Edit
                                                    </Button>
                                                    <Button
                                                        size="sm"
                                                        variant="destructive"
                                                        onClick={() => router.delete(`/customers/${customer.id}`, { preserveScroll: true })}
                                                    >
                                                        <Trash2 data-icon="inline-start" />
                                                        Delete
                                                    </Button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{title}</CardTitle>
                        <CardDescription>Required fields are company, contact, email, and status.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="flex flex-col gap-4">
                            <Field label="Company" error={form.errors.company_name}>
                                <Input value={form.data.company_name} onChange={(e) => form.setData('company_name', e.target.value)} required />
                            </Field>
                            <Field label="Contact" error={form.errors.contact_name}>
                                <Input value={form.data.contact_name} onChange={(e) => form.setData('contact_name', e.target.value)} required />
                            </Field>
                            <Field label="Email" error={form.errors.email}>
                                <Input type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} required />
                            </Field>
                            <div className="grid gap-4 md:grid-cols-2">
                                <Field label="Phone" error={form.errors.phone}>
                                    <Input value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} />
                                </Field>
                                <Field label="Industry" error={form.errors.industry}>
                                    <Input value={form.data.industry} onChange={(e) => form.setData('industry', e.target.value)} />
                                </Field>
                            </div>
                            <div className="grid gap-4 md:grid-cols-2">
                                <Field label="Status" error={form.errors.status}>
                                    <Select value={form.data.status} onValueChange={(value) => form.setData('status', value)}>
                                        <SelectTrigger className="w-full">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                {['lead', 'active', 'inactive', 'suspended'].map((status) => (
                                                    <SelectItem key={status} value={status}>
                                                        {status}
                                                    </SelectItem>
                                                ))}
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </Field>
                                <Field label="Follow Up" error={form.errors.next_follow_up_at}>
                                    <Input type="date" value={form.data.next_follow_up_at} onChange={(e) => form.setData('next_follow_up_at', e.target.value)} />
                                </Field>
                            </div>
                            <Field label="Website" error={form.errors.website}>
                                <Input value={form.data.website} onChange={(e) => form.setData('website', e.target.value)} />
                            </Field>
                            <Field label="Address" error={form.errors.address}>
                                <Input value={form.data.address} onChange={(e) => form.setData('address', e.target.value)} />
                            </Field>
                            <Field label="Notes" error={form.errors.notes}>
                                <Input value={form.data.notes} onChange={(e) => form.setData('notes', e.target.value)} />
                            </Field>
                            <div className="flex gap-2">
                                <Button type="submit" disabled={form.processing}>
                                    <Save data-icon="inline-start" />
                                    {editing ? 'Update' : 'Create'}
                                </Button>
                                <Button type="button" variant="outline" onClick={reset}>
                                    Clear
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

function Field({ label, error, children }: { label: string; error?: string; children: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-2">
            <Label>{label}</Label>
            {children}
            <InputError message={error} />
        </div>
    );
}

CustomersIndex.layout = {
    breadcrumbs: [{ title: 'Customers', href: '/customers' }],
};
