import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'title', header: 'Training Title' },
  { accessorKey: 'trainer', header: 'Trainer' },
  { accessorKey: 'startDate', header: 'Start Date' },
  { accessorKey: 'endDate', header: 'End Date' },
  { accessorKey: 'participants', header: 'Participants' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    title: 'Legal Documentation Best Practices',
    trainer: 'John Expert',
    startDate: '2024-01-15',
    endDate: '2024-01-17',
    participants: 12,
    status: 'Scheduled'
  }
];

export default function TrainingPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Training Management</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Training
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="title"
        searchPlaceholder="Search training programs..."
      />
    </div>
  );
}