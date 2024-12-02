import { Inter } from 'next/font/google'
import './globals.css'
import Sidebar from '@/components/Sidebar'

const inter = Inter({ subsets: ['latin'] })

export const metadata = {
  title: 'MAS ERP System',
  description: 'Law Firm Management System',
}

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <div className="flex min-h-screen">
          <Sidebar />
          <main className="flex-1 p-4 md:p-6 bg-gray-100">
            {children}
          </main>
        </div>
      </body>
    </html>
  )
}
