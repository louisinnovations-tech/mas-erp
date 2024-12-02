import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-react';

const columns = [
  { accessorKey: 'title', header: 'Task' },
  { accessorKey: 'assignee', header: 'Assignee' },
  { accessorKey: 'dueDate', header: 'Due Date' },
  { accessorKey: 'priority', header: 'Priority' },
  { accessorKey: 'status', header: 'Status' },
  { accessorKey: 'progress', header: 'Progress' }
];

const data = [
  {
    id: 1,
    title: 'Document Review',
    assignee: 'Sarah Wilson',
    dueDate: '2024-01-20',
    priority: 'High',
    status: 'In Progress',
    progress: '60%'
  }
];

export default function TasksPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Tasks</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          New Task
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="title"
        searchPlaceholder="Search tasks..."
      />
    </div>
  );
}