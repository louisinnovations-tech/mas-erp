import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'name', header: 'Judge Name' },
  { accessorKey: 'court', header: 'Court' },
  { accessorKey: 'specialization', header: 'Specialization' },
  { accessorKey: 'assignedCases', header: 'Assigned Cases' }
];

const data = [
  {
    id: 1,
    name: 'Hon. Ahmad Al-Mahmoud',
    court: 'Civil Court of Qatar',
    specialization: 'Civil Law',
    assignedCases: 12
  }
];

export default function JudgesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Judges</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Add Judge
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="name"
        searchPlaceholder="Search judges..."
      />
    </div>
  );
}