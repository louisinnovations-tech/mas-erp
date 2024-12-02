import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'number', header: 'Purchase Number' },
  { accessorKey: 'supplier', header: 'Supplier' },
  { accessorKey: 'items', header: 'Items' },
  { accessorKey: 'amount', header: 'Amount' },
  { accessorKey: 'date', header: 'Purchase Date' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    number: 'PUR-2024-001',
    supplier: 'Office Supplies Co.',
    items: 'Office Stationery',
    amount: 1500,
    date: '2024-01-15',
    status: 'Received'
  }
];

export default function PurchasesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Local Purchases</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Purchase
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="number"
        searchPlaceholder="Search purchases..."
      />
    </div>
  );
}