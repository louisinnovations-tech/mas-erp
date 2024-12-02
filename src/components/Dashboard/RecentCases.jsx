import { Badge } from "@/components/ui/badge";

const cases = [
  {
    id: 1,
    title: "Smith vs. Johnson",
    type: "Civil",
    status: "Active",
    date: "2024-01-15"
  },
  {
    id: 2,
    title: "State vs. Williams",
    type: "Criminal",
    status: "Pending",
    date: "2024-01-14"
  },
  {
    id: 3,
    title: "Davis Family Trust",
    type: "Estate",
    status: "Active",
    date: "2024-01-13"
  },
  {
    id: 4,
    title: "Brown Property Dispute",
    type: "Property",
    status: "Active",
    date: "2024-01-12"
  }
];

export function RecentCases() {
  return (
    <div className="space-y-8">
      {cases.map((case_) => (
        <div key={case_.id} className="flex items-center">
          <div className="space-y-1">
            <p className="text-sm font-medium leading-none">{case_.title}</p>
            <p className="text-sm text-muted-foreground">{case_.type}</p>
          </div>
          <div className="ml-auto flex items-center gap-2">
            <Badge variant={case_.status === 'Active' ? 'default' : 'secondary'}>
              {case_.status}
            </Badge>
          </div>
        </div>
      ))}
    </div>
  );
}