import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'employee', header: 'Employee' },
  { accessorKey: 'type', header: 'Leave Type' },
  { accessorKey: 'startDate', header: 'Start Date' },
  { accessorKey: 'endDate', header: 'End Date' },
  { accessorKey: 'days', header: 'Days' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    employee: 'Sarah Wilson',
    type: 'Annual Leave',
    startDate: '2024-01-15',
    endDate: '2024-01-20',
    days: 5,
    status: 'Approved'
  }
];

export default function LeavePage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Leave Management</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Apply Leave
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="employee"
        searchPlaceholder="Search leave records..."
      />
    </div>
  );
}