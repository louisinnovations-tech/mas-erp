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
    // ... rest of the menu items
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

      <div className={`${isOpen ? 'translate-x-0' : '-translate-x-full'} 
        transition-transform duration-200 ease-in-out fixed md:static 
        bg-white border-r border-accent h-full w-64 min-w-64 overflow-y-auto 
        z-40 md:translate-x-0 shadow-sm`}>
        <div className="p-4 space-y-4">
          <div className="font-bold text-xl px-4 text-[#800000]">MAS ERP</div>
          {menuItems.map((item, index) => (
            <div key={index} className="space-y-1">
              {item.items ? (
                <details className="group [&_summary::-webkit-details-marker]:hidden">
                  <summary className="flex cursor-pointer items-center justify-between 
                    rounded-lg px-4 py-2 text-[#576071] hover:bg-accent">
                    <span className="font-medium">{item.title}</span>
                    <ChevronDown className="h-5 w-5 transition-transform 
                      group-open:rotate-180" />
                  </summary>
                  <nav className="mt-2 flex flex-col px-4">
                    {item.items.map((subItem, subIndex) => (
                      <Link
                        key={subIndex}
                        href={subItem.href}
                        className={`rounded-lg px-4 py-2 text-sm 
                          ${pathname === subItem.href ? 
                            'bg-accent text-[#800000] font-medium' : 
                            'text-[#576071] hover:bg-accent'}`}
                      >
                        {subItem.title}
                      </Link>
                    ))}
                  </nav>
                </details>
              ) : (
                <Link
                  href={item.href}
                  className={`flex items-center rounded-lg px-4 py-2 
                    ${pathname === item.href ? 
                      'bg-accent text-[#800000] font-medium' : 
                      'text-[#576071] hover:bg-accent'}`}
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