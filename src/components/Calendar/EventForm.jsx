import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { useToast } from '@/components/ui/use-toast';
import { VideoIcon } from 'lucide-react';

export function EventForm({ date, event, onClose }) {
  const { toast } = useToast();
  const [isZoomMeeting, setIsZoomMeeting] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
      title: formData.get('title'),
      date: formData.get('date'),
      startTime: formData.get('startTime'),
      endTime: formData.get('endTime'),
      type: formData.get('type'),
      description: formData.get('description'),
      isZoomMeeting: isZoomMeeting,
    };

    try {
      // API call would go here
      toast({
        title: 'Success',
        description: `Event ${event ? 'updated' : 'created'} successfully`,
      });
      onClose();
    } catch (error) {
      toast({
        variant: 'destructive',
        title: 'Error',
        description: 'Something went wrong. Please try again.',
      });
    }
  };

  const eventTypes = [
    'Trial',
    'Meeting',
    'Consultation',
    'Task Deadline',
    'Other'
  ];

  return (
    <form onSubmit={handleSubmit} className="space-y-4 bg-background border rounded-lg p-4">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="title">Event Title</Label>
          <Input
            id="title"
            name="title"
            defaultValue={event?.title}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="type">Event Type</Label>
          <Select name="type" defaultValue={event?.type}>
            <SelectTrigger>
              <SelectValue placeholder="Select type" />
            </SelectTrigger>
            <SelectContent>
              {eventTypes.map((type) => (
                <SelectItem key={type} value={type}>
                  {type}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label htmlFor="date">Date</Label>
          <Input
            id="date"
            name="date"
            type="date"
            defaultValue={date?.toISOString().split('T')[0] || event?.date}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="startTime">Start Time</Label>
          <Input
            id="startTime"
            name="startTime"
            type="time"
            defaultValue={event?.startTime}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="endTime">End Time</Label>
          <Input
            id="endTime"
            name="endTime"
            type="time"
            defaultValue={event?.endTime}
            required
          />
        </div>

        <div className="space-y-2 flex items-center justify-between">
          <Label htmlFor="zoom">Create Zoom Meeting</Label>
          <Switch
            id="zoom"
            checked={isZoomMeeting}
            onCheckedChange={setIsZoomMeeting}
          />
        </div>
      </div>

      {isZoomMeeting && (
        <div className="flex items-center space-x-2 text-muted-foreground">
          <VideoIcon className="h-4 w-4" />
          <span className="text-sm">Zoom meeting link will be generated automatically</span>
        </div>
      )}

      <div className="flex justify-end space-x-2">
        <Button type="button" variant="outline" onClick={onClose}>
          Cancel
        </Button>
        <Button type="submit">
          {event ? 'Update' : 'Create'} Event
        </Button>
      </div>
    </form>
  );
}