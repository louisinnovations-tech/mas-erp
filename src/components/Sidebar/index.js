import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { ChevronDown, Menu } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function Sidebar() {
  const [isOpen, setIsOpen] = useState(true);
  const pathname = usePathname();

  const menuItems = [
    { title: 'Dashboard', href: '/' },
    {
      title: 'Stakeholders',
      items: [
        { title: 'Clients', href: '/clients' },
        { title: 'Employees', href: '/employees' }
      ]
    },
    { title: 'Case Register', href: '/cases' },
    {
      title: 'Law Management',
      items: [
        { title: 'Client Requests', href: '/law/client-requests' },
        { title: 'Area of Practices', href: '/law/practices' },
        { title: 'Courts', href: '/law/courts' },
        { title: 'Judges', href: '/law/judges' },
        { title: 'Acts & Articles', href: '/law/acts' },
        { title: 'Matter', href: '/law/matter' }
      ]
    },
    {
      title: 'Finance',
      items: [
        { title: 'Proforma Invoices', href: '/finance/proforma' },
        { title: 'Invoices', href: '/finance/invoices' },
        { title: 'Quotations', href: '/finance/quotations' },
        { title: 'Local Purchases', href: '/finance/purchases' },
        { title: 'Services Hired', href: '/finance/services' },
        { title: 'Estimate', href: '/finance/estimates' },
        { title: 'Credit Note', href: '/finance/credit-notes' }
      ]
    },
    { title: 'Tasks', href: '/tasks' },
    { title: 'Calendar', href: '/calendar' },
    {
      title: 'HRM',
      items: [
        { title: 'Timesheet', href: '/hrm/timesheet' },
        { title: 'Payroll', href: '/hrm/payroll' },
        { title: 'Leave', href: '/hrm/leave' },
        { title: 'Attendance', href: '/hrm/attendance' },
        { title: 'Employee Data', href: '/hrm/employees' },
        { title: 'Training', href: '/hrm/training' },
        { title: 'HR Admin', href: '/hrm/admin' },
        { title: 'Recruitment', href: '/hrm/recruitment' },
        { title: 'Meetings', href: '/hrm/meetings' },
        { title: 'Policies', href: '/hrm/policies' }
      ]
    },
    {
      title: 'CRM',
      items: [
        { title: 'Walk-in Leads', href: '/crm/walk-in' },
        { title: 'Virtual Meetings', href: '/crm/meetings' }
      ]
    },
    {
      title: 'Inventory',
      items: [
        { title: 'Suppliers', href: '/inventory/suppliers' },
        { title: 'Items', href: '/inventory/items' }
      ]
    }
  ];

  return (
    <>
      <Button
        variant="ghost"
        className="fixed top-4 left-4 md:hidden z-50"
        onClick={() => setIsOpen(!isOpen)}
      >
        <Menu className="h-4 w-4" />
      </Button>

      <div className={`${isOpen ? 'translate-x-0' : '-translate-x-full'} transition-transform duration-200 ease-in-out fixed md:static bg-white border-r h-full w-64 min-w-64 overflow-y-auto z-40 md:translate-x-0`}>
        <div className="p-4 space-y-4">
          <div className="font-bold text-xl px-4">MAS ERP</div>
          {menuItems.map((item, index) => (
            <div key={index} className="space-y-1">
              {item.items ? (
                <details className="group [&_summary::-webkit-details-marker]:hidden">
                  <summary className="flex cursor-pointer items-center justify-between rounded-lg px-4 py-2 hover:bg-gray-100">
                    <span className="font-medium">{item.title}</span>
                    <ChevronDown className="h-5 w-5 transition-transform group-open:rotate-180" />
                  </summary>
                  <nav className="mt-2 flex flex-col px-4">
                    {item.items.map((subItem, subIndex) => (
                      <Link
                        key={subIndex}
                        href={subItem.href}
                        className={`rounded-lg px-4 py-2 text-sm ${pathname === subItem.href ? 'bg-gray-100 font-medium' : 'hover:bg-gray-50'}`}
                      >
                        {subItem.title}
                      </Link>
                    ))}
                  </nav>
                </details>
              ) : (
                <Link
                  href={item.href}
                  className={`flex items-center rounded-lg px-4 py-2 ${pathname === item.href ? 'bg-gray-100 font-medium' : 'hover:bg-gray-50'}`}
                >
                  {item.title}
                </Link>
              )}
            </div>
          ))}
        </div>
      </div>
    </>
  );
}