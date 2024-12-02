import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'number', header: 'Service Number' },
  { accessorKey: 'provider', header: 'Service Provider' },
  { accessorKey: 'service', header: 'Service' },
  { accessorKey: 'amount', header: 'Amount' },
  { accessorKey: 'startDate', header: 'Start Date' },
  { accessorKey: 'endDate', header: 'End Date' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    number: 'SER-2024-001',
    provider: 'Clean Co.',
    service: 'Office Cleaning',
    amount: 2000,
    startDate: '2024-01-01',
    endDate: '2024-12-31',
    status: 'Active'
  }
];

export default function ServicesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Services Hired</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Service
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="number"
        searchPlaceholder="Search services..."
      />
    </div>
  );
}