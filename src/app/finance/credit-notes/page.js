import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'number', header: 'Credit Note Number' },
  { accessorKey: 'invoice', header: 'Invoice Reference' },
  { accessorKey: 'client', header: 'Client' },
  { accessorKey: 'amount', header: 'Amount' },
  { accessorKey: 'date', header: 'Issue Date' },
  { accessorKey: 'reason', header: 'Reason' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    number: 'CN-2024-001',
    invoice: 'INV-2024-001',
    client: 'John Smith',
    amount: 500,
    date: '2024-01-15',
    reason: 'Service adjustment',
    status: 'Processed'
  }
];

export default function CreditNotesPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Credit Notes</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Credit Note
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="number"
        searchPlaceholder="Search credit notes..."
      />
    </div>
  );
}