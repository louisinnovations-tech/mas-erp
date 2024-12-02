import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'name', header: 'Practice Area' },
  { accessorKey: 'description', header: 'Description' },
  { accessorKey: 'cases', header: 'Active Cases' }
];

const data = [
  {
    id: 1,
    name: 'Civil Law',
    description: 'Handling civil disputes and litigation',
    cases: 15
  }
];

export default function PracticesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Areas of Practice</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Add Practice Area
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="name"
        searchPlaceholder="Search practice areas..."
      />
    </div>
  );
}