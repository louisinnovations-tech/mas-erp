import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'position', header: 'Position' },
  { accessorKey: 'department', header: 'Department' },
  { accessorKey: 'openings', header: 'Openings' },
  { accessorKey: 'applications', header: 'Applications' },
  { accessorKey: 'postDate', header: 'Posted Date' },
  { accessorKey: 'deadline', header: 'Deadline' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    position: 'Legal Assistant',
    department: 'Legal',
    openings: 2,
    applications: 15,
    postDate: '2024-01-01',
    deadline: '2024-01-31',
    status: 'Active'
  }
];

export default function RecruitmentPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Recruitment</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Post Job
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="position"
        searchPlaceholder="Search job postings..."
      />
    </div>
  );
}