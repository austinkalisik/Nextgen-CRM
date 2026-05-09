import { Head, Link } from '@inertiajs/react';
import { Building2, Globe2, ShieldCheck, Users } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { DomainHostingRequest } from '@/types';

type Props = {
    metrics: {
        customers: number;
        activeCustomers: number;
        openRequests: number;
        users: number;
    };
    recentRequests: DomainHostingRequest[];
};

const serviceMix = [
    ['Domain Hosting', 'Plans, .pg registration, cPanel, renewals'],
    ['Email Security', 'POP3 accounts, spam and virus protection'],
    ['ISP & Networks', 'Fiber, VSAT, structured cabling'],
    ['Security', 'AI CCTV, access control, monitoring'],
    ['Digital Systems', 'Dokmee, websites, app development'],
];

const statusVariant = (status: string) =>
    ['completed', 'approved'].includes(status) ? 'default' : 'secondary';

export default function Dashboard({ metrics, recentRequests }: Props) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-4 p-4">
                <div className="rounded-lg border bg-card/80 p-5 shadow-2xl shadow-black/20">
                    <h1 className="text-3xl font-semibold tracking-tight">
                        NextGen CRM
                    </h1>
                    <p className="max-w-3xl text-sm text-muted-foreground">
                        Premium operations hub for Papua New Guinea domain hosting, email protection, ISP, CCTV, document management, web development, quotes, renewals, and support.
                    </p>
                </div>

                <div className="grid gap-4 md:grid-cols-4">
                    <Metric title="Customers" value={metrics.customers} icon={Building2} />
                    <Metric title="Active Customers" value={metrics.activeCustomers} icon={Building2} />
                    <Metric title="Open Requests" value={metrics.openRequests} icon={Globe2} />
                    <Metric title="Users" value={metrics.users} icon={Users} />
                </div>

                <div className="grid gap-4 xl:grid-cols-[1fr_360px]">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-4">
                            <div>
                                <CardTitle>Recent Service Requests</CardTitle>
                                <CardDescription>
                                    Latest hosting, security, ISP, document, and development work.
                                </CardDescription>
                            </div>
                            <Button asChild size="sm">
                                <Link href="/hosting-requests">Manage</Link>
                            </Button>
                        </CardHeader>
                        <CardContent>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b text-left text-muted-foreground">
                                            <th className="py-2 font-medium">Customer</th>
                                            <th className="py-2 font-medium">Domain/Asset</th>
                                            <th className="py-2 font-medium">Service</th>
                                            <th className="py-2 font-medium">Status</th>
                                            <th className="py-2 text-right font-medium">Quote</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {recentRequests.map((request) => (
                                            <tr key={request.id} className="border-b last:border-0">
                                                <td className="py-3">{request.customer?.company_name}</td>
                                                <td className="py-3 font-medium">{request.domain_name}</td>
                                                <td className="py-3">{request.service_type.replaceAll('_', ' ')}</td>
                                                <td className="py-3">
                                                    <Badge variant={statusVariant(request.status)}>
                                                        {request.status.replaceAll('_', ' ')}
                                                    </Badge>
                                                </td>
                                                <td className="py-3 text-right">
                                                    {request.quoted_amount ? `PGK ${request.quoted_amount}` : 'Pending'}
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
                            <CardTitle>NextGen Service Desk</CardTitle>
                            <CardDescription>Operational coverage for the full ICT portfolio.</CardDescription>
                        </CardHeader>
                        <CardContent className="flex flex-col gap-3">
                            {serviceMix.map(([name, description]) => (
                                <div key={name} className="flex gap-3 rounded-md border bg-secondary/40 p-3">
                                    <ShieldCheck className="mt-0.5 text-primary" />
                                    <div>
                                        <div className="font-medium">{name}</div>
                                        <div className="text-xs text-muted-foreground">{description}</div>
                                    </div>
                                </div>
                            ))}
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

function Metric({
    title,
    value,
    icon: Icon,
}: {
    title: string;
    value: number;
    icon: React.ElementType;
}) {
    return (
        <Card>
            <CardHeader className="flex flex-row items-center justify-between gap-3 pb-2">
                <CardDescription>{title}</CardDescription>
                <Icon className="text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div className="text-3xl font-semibold">{value}</div>
            </CardContent>
        </Card>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }],
};
