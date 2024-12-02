import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'id', header: 'Request ID' },
  { accessorKey: 'client', header: 'Client' },
  { accessorKey: 'practice', header: 'Area of Practice' },
  { accessorKey: 'date', header: 'Request Date' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 'REQ-2024-001',
    client: 'John Smith',
    practice: 'Civil Law',
    date: '2024-01-15',
    status: 'Open'
  }
];

export default function ClientRequestsPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Client Requests</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Request
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="id"
        searchPlaceholder="Search requests..."
      />
    </div>
  );
}