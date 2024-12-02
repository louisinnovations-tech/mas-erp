import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Plus, Trash } from 'lucide-react';

export function Trial({ trials = [] }) {
  const [trialList, setTrialList] = useState(trials);

  const addTrial = () => {
    setTrialList([...trialList, { date: '', time: '', notes: '' }]);
  };

  const removeTrial = (index) => {
    setTrialList(trialList.filter((_, i) => i !== index));
  };

  const updateTrial = (index, field, value) => {
    const updated = trialList.map((trial, i) => 
      i === index ? { ...trial, [field]: value } : trial
    );
    setTrialList(updated);
  };

  return (
    <div className="space-y-4">
      <div className="flex justify-between items-center">
        <h3 className="text-lg font-semibold">Trials</h3>
        <Button type="button" onClick={addTrial} variant="outline" size="sm">
          <Plus className="h-4 w-4 mr-2" />
          Add Trial
        </Button>
      </div>

      {trialList.map((trial, index) => (
        <div key={index} className="space-y-4 p-4 border rounded-lg">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label>Date</Label>
              <Input
                type="date"
                value={trial.date}
                onChange={(e) => updateTrial(index, 'date', e.target.value)}
              />
            </div>

            <div className="space-y-2">
              <Label>Time</Label>
              <Input
                type="time"
                value={trial.time}
                onChange={(e) => updateTrial(index, 'time', e.target.value)}
              />
            </div>
          </div>

          <div className="space-y-2">
            <Label>Notes</Label>
            <div className="flex gap-2">
              <Textarea
                value={trial.notes}
                onChange={(e) => updateTrial(index, 'notes', e.target.value)}
                placeholder="Trial notes"
              />
              <Button
                type="button"
                variant="outline"
                size="icon"
                onClick={() => removeTrial(index)}
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