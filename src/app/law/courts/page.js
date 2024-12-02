import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'name', header: 'Court Name' },
  { accessorKey: 'type', header: 'Court Type' },
  { accessorKey: 'location', header: 'Location' },
  { accessorKey: 'activeCases', header: 'Active Cases' }
];

const data = [
  {
    id: 1,
    name: 'Civil Court of Qatar',
    type: 'Civil Court',
    location: 'Doha',
    activeCases: 25
  }
];

export default function CourtsPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Courts</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Add Court
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="name"
        searchPlaceholder="Search courts..."
      />
    </div>
  );
}