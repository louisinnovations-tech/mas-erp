import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  {
    accessorKey: 'caseNumber',
    header: 'Case Number'
  },
  {
    accessorKey: 'client',
    header: 'Client'
  },
  {
    accessorKey: 'type',
    header: 'Type'
  },
  {
    accessorKey: 'attorney',
    header: 'Attorney'
  },
  {
    accessorKey: 'court',
    header: 'Court'
  },
  {
    accessorKey: 'nextHearing',
    header: 'Next Hearing'
  },
  {
    accessorKey: 'status',
    header: 'Status'
  }
];

const data = [
  {
    id: 1,
    caseNumber: 'CASE-2024-001',
    client: 'John Smith',
    type: 'Civil',
    attorney: 'Sarah Wilson',
    court: 'Civil Court',
    nextHearing: '2024-01-15',
    status: 'Active'
  }
];

export default function CasesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Case Register</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Register Case
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="caseNumber"
        searchPlaceholder="Search cases..."
      />
    </div>
  );
}