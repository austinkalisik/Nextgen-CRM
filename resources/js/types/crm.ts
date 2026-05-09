export type Customer = {
    id: number;
    company_name: string;
    contact_name: string;
    email: string;
    phone: string | null;
    industry: string | null;
    status: 'lead' | 'active' | 'inactive' | 'suspended';
    website: string | null;
    address: string | null;
    notes: string | null;
    next_follow_up_at: string | null;
    domain_hosting_requests_count?: number;
    created_at: string;
    updated_at: string;
};

export type DomainHostingRequest = {
    id: number;
    customer_id: number;
    assigned_to: number | null;
    domain_name: string;
    service_type: string;
    plan: string;
    status: string;
    requested_start_date: string | null;
    renewal_date: string | null;
    quoted_amount: string | number | null;
    requirements: string | null;
    internal_notes: string | null;
    customer?: Pick<Customer, 'id' | 'company_name'>;
    assignee?: { id: number; name: string } | null;
    created_at: string;
    updated_at: string;
};
