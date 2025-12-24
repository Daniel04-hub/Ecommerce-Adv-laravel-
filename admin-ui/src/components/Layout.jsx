import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export default function Layout({ children }) {
  const { logout } = useAuth();
  const location = useLocation();

  const isActive = (path) => location.pathname === path;

  return (
    <div className="min-h-screen bg-gray-100">
      {/* Sidebar */}
      <div className="fixed inset-y-0 left-0 w-64 bg-gray-900 text-white">
        <div className="p-6">
          <h1 className="text-2xl font-bold">Admin Dashboard</h1>
          <p className="text-sm text-gray-400">E-Commerce Platform</p>
        </div>
        
        <nav className="mt-6">
          <Link
            to="/"
            className={`block px-6 py-3 ${isActive('/') ? 'bg-gray-800 border-l-4 border-blue-500' : 'hover:bg-gray-800'}`}
          >
            📊 Dashboard
          </Link>
          <Link
            to="/users"
            className={`block px-6 py-3 ${isActive('/users') ? 'bg-gray-800 border-l-4 border-blue-500' : 'hover:bg-gray-800'}`}
          >
            👥 Users
          </Link>
          <Link
            to="/vendors"
            className={`block px-6 py-3 ${isActive('/vendors') ? 'bg-gray-800 border-l-4 border-blue-500' : 'hover:bg-gray-800'}`}
          >
            🏪 Vendors
          </Link>
          <Link
            to="/products"
            className={`block px-6 py-3 ${isActive('/products') ? 'bg-gray-800 border-l-4 border-blue-500' : 'hover:bg-gray-800'}`}
          >
            📦 Products
          </Link>
          <Link
            to="/orders"
            className={`block px-6 py-3 ${isActive('/orders') ? 'bg-gray-800 border-l-4 border-blue-500' : 'hover:bg-gray-800'}`}
          >
            🛒 Orders
          </Link>
        </nav>

        <div className="absolute bottom-0 w-64 p-6 border-t border-gray-800">
          <button
            onClick={logout}
            className="w-full px-4 py-2 text-sm bg-red-600 hover:bg-red-700 rounded"
          >
            Logout
          </button>
        </div>
      </div>

      {/* Main Content */}
      <div className="ml-64">
        {/* Header */}
        <header className="bg-white shadow">
          <div className="px-8 py-6">
            <h2 className="text-3xl font-bold text-gray-800">
              {location.pathname === '/' && 'Dashboard'}
              {location.pathname === '/users' && 'Users Management'}
              {location.pathname === '/vendors' && 'Vendors Management'}
              {location.pathname === '/products' && 'Products Management'}
              {location.pathname === '/orders' && 'Orders Monitoring'}
            </h2>
          </div>
        </header>

        {/* Page Content */}
        <main className="p-8">
          {children}
        </main>
      </div>
    </div>
  );
}
