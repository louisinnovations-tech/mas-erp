import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { FileDown } from 'lucide-react';

const columns = [
  { accessorKey: 'employee', header: 'Employee' },
  { accessorKey: 'period', header: 'Pay Period' },
  { accessorKey: 'basic', header: 'Basic Salary' },
  { accessorKey: 'allowances', header: 'Allowances' },
  { accessorKey: 'deductions', header: 'Deductions' },
  { accessorKey: 'net', header: 'Net Salary' },
  { accessorKey: 'status', header: 'Status' }
];

const data = [
  {
    id: 1,
    employee: 'Sarah Wilson',
    period: 'January 2024',
    basic: 5000,
    allowances: 1000,
    deductions: 500,
    net: 5500,
    status: 'Processed'
  }
];

export default function PayrollPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Payroll Management</h1>
        <div className="space-x-2">
          <Button>
            <Plus className="mr-2 h-4 w-4" />
            Process Payroll
          </Button>
          <Button variant="outline">
            <FileDown className="mr-2 h-4 w-4" />
            Export
          </Button>
        </div>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="employee"
        searchPlaceholder="Search payroll records..."
      />
    </div>
  );
}