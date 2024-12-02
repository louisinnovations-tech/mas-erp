import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { useToast } from '@/components/ui/use-toast';

export function EmployeeForm({ employee, onSubmit, onCancel }) {
  const { toast } = useToast();

  const handleSubmit = async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = {
      name: formData.get('name'),
      email: formData.get('email'),
      mobile: formData.get('mobile'),
      position: formData.get('position'),
      department: formData.get('department'),
      joiningDate: formData.get('joiningDate'),
    };

    try {
      await onSubmit(data);
      toast({
        title: 'Success',
        description: `Employee ${employee ? 'updated' : 'created'} successfully`,
      });
    } catch (error) {
      toast({
        variant: 'destructive',
        title: 'Error',
        description: 'Something went wrong. Please try again.',
      });
    }
  };

  const positions = [
    'Attorney',
    'Paralegal',
    'Legal Secretary',
    'Office Manager',
    'Accountant',
    'HR Manager',
    'IT Support',
    'Administrative Staff'
  ];

  const departments = [
    'Legal',
    'Administration',
    'Finance',
    'Human Resources',
    'IT',
    'Operations'
  ];

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="space-y-2">
          <Label htmlFor="name">Full Name</Label>
          <Input
            id="name"
            name="name"
            defaultValue={employee?.name}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="email">Email</Label>
          <Input
            id="email"
            name="email"
            type="email"
            defaultValue={employee?.email}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="mobile">Mobile Number</Label>
          <Input
            id="mobile"
            name="mobile"
            defaultValue={employee?.mobile}
            required
          />
        </div>

        <div className="space-y-2">
          <Label htmlFor="position">Position</Label>
          <Select name="position" defaultValue={employee?.position}>
            <SelectTrigger>
              <SelectValue placeholder="Select position" />
            </SelectTrigger>
            <SelectContent>
              {positions.map((position) => (
                <SelectItem key={position} value={position}>
                  {position}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label htmlFor="department">Department</Label>
          <Select name="department" defaultValue={employee?.department}>
            <SelectTrigger>
              <SelectValue placeholder="Select department" />
            </SelectTrigger>
            <SelectContent>
              {departments.map((department) => (
                <SelectItem key={department} value={department}>
                  {department}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        <div className="space-y-2">
          <Label htmlFor="joiningDate">Joining Date</Label>
          <Input
            id="joiningDate"
            name="joiningDate"
            type="date"
            defaultValue={employee?.joiningDate}
            required
          />
        </div>
      </div>

      <div className="flex justify-end space-x-2">
        <Button type="button" variant="outline" onClick={onCancel}>
          Cancel
        </Button>
        <Button type="submit">
          {employee ? 'Update' : 'Create'} Employee
        </Button>
      </div>
    </form>
  );
}