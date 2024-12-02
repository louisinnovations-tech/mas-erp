import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'name', header: 'Supplier Name' },
  { accessorKey: 'contact', header: 'Contact Person' },
  { accessorKey: 'email', header: 'Email' },
  { accessorKey: 'phone', header: 'Phone' },
  { accessorKey: 'address', header: 'Address' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    name: 'Office Supplies Co.',
    contact: 'Ahmed Mohammed',
    email: 'ahmed@suppliesqatar.com',
    phone: '+974 5555 1234',
    address: 'Industrial Area, Doha',
    status: 'Active'
  }
];

export default function SuppliersPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Suppliers</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Add Supplier
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="name"
        searchPlaceholder="Search suppliers..."
      />
    </div>
  );
}