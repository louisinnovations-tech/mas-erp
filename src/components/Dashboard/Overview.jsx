import { Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

const data = [
  { name: 'Jan', cases: 10, revenue: 4000 },
  { name: 'Feb', cases: 15, revenue: 6000 },
  { name: 'Mar', cases: 12, revenue: 5200 },
  { name: 'Apr', cases: 18, revenue: 7800 },
  { name: 'May', cases: 22, revenue: 9400 },
  { name: 'Jun', cases: 20, revenue: 8600 },
  { name: 'Jul', cases: 25, revenue: 11000 },
];

export function Overview() {
  return (
    <ResponsiveContainer width="100%" height={350}>
      <LineChart data={data}>
        <XAxis dataKey="name" className="text-sm" />
        <YAxis className="text-sm" />
        <Tooltip />
        <Line type="monotone" dataKey="cases" stroke="#2563eb" strokeWidth={2} />
        <Line type="monotone" dataKey="revenue" stroke="#16a34a" strokeWidth={2} />
      </LineChart>
    </ResponsiveContainer>
  );
}