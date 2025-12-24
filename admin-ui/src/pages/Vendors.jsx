import React, { useState, useEffect } from 'react';
import { vendorsApi } from '../api/client';

export default function Vendors() {
  const [vendors, setVendors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    loadVendors();
  }, []);

  const loadVendors = async () => {
    try {
      setLoading(true);
      const response = await vendorsApi.getAll();
      setVendors(response.data.data || response.data);
      setError(null);
    } catch (err) {
      setError(err.response?.data?.message || 'Failed to load vendors');
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id) => {
    if (!confirm('Approve this vendor?')) return;
    
    try {
      await vendorsApi.approve(id);
      await loadVendors();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to approve vendor');
    }
  };

  const handleBlock = async (id) => {
    if (!confirm('Block this vendor?')) return;
    
    try {
      await vendorsApi.block(id);
      await loadVendors();
    } catch (err) {
      alert(err.response?.data?.message || 'Failed to block vendor');
    }
  };

  if (loading) {
    return <div className="text-center py-12">Loading vendors...</div>;
  }

  if (error) {
    return (
      <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {error}
      </div>
    );
  }

  return (
    <div className="bg-white rounded-lg shadow">
      <div className="p-6 border-b">
        <div className="flex justify-between items-center">
          <h3 className="text-lg font-semibold">All Vendors</h3>
          <span className="text-sm text-gray-600">{vendors.length} total vendors</span>
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full">
          <thead className="bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {vendors.map((vendor) => (
              <tr key={vendor.id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-sm">{vendor.id}</td>
                <td className="px-6 py-4 text-sm font-medium">{vendor.name}</td>
                <td className="px-6 py-4 text-sm text-gray-600">{vendor.email}</td>
                <td className="px-6 py-4 text-sm">
                  <span className={`px-2 py-1 rounded text-xs font-semibold ${
                    vendor.is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                  }`}>
                    {vendor.is_approved ? 'Approved' : 'Pending'}
                  </span>
                </td>
                <td className="px-6 py-4 text-sm">
                  {!vendor.is_approved && (
                    <button
                      onClick={() => handleApprove(vendor.id)}
                      className="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 mr-2"
                    >
                      Approve
                    </button>
                  )}
                  <button
                    onClick={() => handleBlock(vendor.id)}
                    className="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                  >
                    Block
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
