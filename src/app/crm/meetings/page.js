import { DataTable } from '@/components/ui/data-table';
import { Button } from '@/components/ui/button';
import { Plus, VideoIcon } from 'lucide-react';

const columns = [
  { accessorKey: 'client', header: 'Client' },
  { accessorKey: 'topic', header: 'Topic' },
  { accessorKey: 'date', header: 'Date' },
  { accessorKey: 'time', header: 'Time' },
  { accessorKey: 'duration', header: 'Duration' },
  { accessorKey: 'type', header: 'Type' },
  { accessorKey: 'status', header: 'Status' },
  {
    id: 'actions',
    cell: ({ row }) => {
      return (
        <Button variant="ghost" size="icon">
          <VideoIcon className="h-4 w-4" />
        </Button>
      );
    },
  }
];

const data = [
  {
    id: 1,
    client: 'John Smith',
    topic: 'Initial Consultation',
    date: '2024-01-15',
    time: '10:00 AM',
    duration: '30 minutes',
    type: 'Zoom',
    status: 'Scheduled'
  }
];

export default function MeetingsPage() {
  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Virtual Meetings</h1>
        <Button>
          <Plus className="mr-2 h-4 w-4" />
          Schedule Meeting
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={data}
        searchKey="client"
        searchPlaceholder="Search meetings..."
      />
    </div>
  );
}