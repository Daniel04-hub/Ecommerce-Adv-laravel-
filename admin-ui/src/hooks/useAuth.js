import { useState, useEffect } from 'react';

export function useAuth() {
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const token = localStorage.getItem('admin_token');
    setIsAuthenticated(!!token);
    setLoading(false);
  }, []);

  const logout = () => {
    localStorage.removeItem('admin_token');
    window.location.href = '/login';
  };

  return { isAuthenticated, loading, logout };
}
