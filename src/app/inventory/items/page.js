import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'itemNumber', header: 'Item #' },
  { accessorKey: 'name', header: 'Item Name' },
  { accessorKey: 'type', header: 'Type' },
  { accessorKey: 'supplier', header: 'Supplier' },
  { accessorKey: 'quantity', header: 'Quantity' },
  { accessorKey: 'location', header: 'Location' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    itemNumber: 'ITM-2024-001',
    name: 'Office Desk',
    type: 'Furniture',
    supplier: 'Office Supplies Co.',
    quantity: 5,
    location: 'Main Office',
    status: 'In Stock'
  }
];

export default function InventoryPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Inventory Items</h1>
        <div className="space-x-2">
          <Button variant="outline">
            Generate Report
          </Button>
          <Button>
            <Plus className="mr-2 h-4 w-4" />
            Add Item
          </Button>
        </div>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="name"
        searchPlaceholder="Search inventory items..."
      />
    </div>
  );
}