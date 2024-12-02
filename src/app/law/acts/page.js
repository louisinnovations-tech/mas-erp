import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'number', header: 'Act Number' },
  { accessorKey: 'title', header: 'Title' },
  { accessorKey: 'category', header: 'Category' },
  { accessorKey: 'date', header: 'Effective Date' }
];

const data = [
  {
    id: 1,
    number: 'Act-2023-15',
    title: 'Civil Procedure Code',
    category: 'Civil Law',
    date: '2023-06-01'
  }
];

export default function ActsPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Acts & Articles</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Add Act
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="title"
        searchPlaceholder="Search acts..."
      />
    </div>
  );
}