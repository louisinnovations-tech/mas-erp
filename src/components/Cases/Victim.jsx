import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Plus, Trash } from 'lucide-react';

export function Victim({ victims = [] }) {
  const [victimList, setVictimList] = useState(victims);

  const addVictim = () => {
    setVictimList([...victimList, { name: '', contact: '' }]);
  };

  const removeVictim = (index) => {
    setVictimList(victimList.filter((_, i) => i !== index));
  };

  const updateVictim = (index, field, value) => {
    const updated = victimList.map((victim, i) => 
      i === index ? { ...victim, [field]: value } : victim
    );
    setVictimList(updated);
  };

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h3 className="text-lg font-semibold">Victims</h3>
        <Button type="button" onClick={addVictim} variant="outline" size="sm">
          <Plus className="h-4 w-4 mr-2" />
          Add Victim
        </Button>
      </div>

      {victimList.map((victim, index) => (
        <div key={index} className="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border rounded-lg">
          <div className="space-y-2">
            <Label>Name</Label>
            <Input
              value={victim.name}
              onChange={(e) => updateVictim(index, 'name', e.target.value)}
              placeholder="Victim name"
            />
          </div>

          <div className="space-y-2">
            <Label>Contact Number</Label>
            <div className="flex gap-2">
              <Input
                value={victim.contact}
                onChange={(e) => updateVictim(index, 'contact', e.target.value)}
                placeholder="Contact number"
              />
              <Button
                type="button"
                variant="outline"
                size="icon"
                onClick={() => removeVictim(index)}
              >
                <Trash className="h-4 w-4" />
              </Button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}