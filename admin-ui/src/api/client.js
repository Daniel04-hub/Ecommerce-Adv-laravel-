import axios from 'axios';

const apiClient = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Add token to requests
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('admin_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Handle 401 responses
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('admin_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;

// API endpoints
export const dashboardApi = {
  getStats: () => apiClient.get('/admin/dashboard/stats'),
};

export const usersApi = {
  getAll: (params) => apiClient.get('/admin/users', { params }),
  getOne: (id) => apiClient.get(`/admin/users/${id}`),
  create: (data) => apiClient.post('/admin/users', data),
  update: (id, data) => apiClient.put(`/admin/users/${id}`, data),
  delete: (id) => apiClient.delete(`/admin/users/${id}`),
};

export const vendorsApi = {
  getAll: (params) => apiClient.get('/admin/vendors', { params }),
  getOne: (id) => apiClient.get(`/admin/vendors/${id}`),
  approve: (id) => apiClient.patch(`/admin/vendors/${id}/approve`),
  block: (id) => apiClient.patch(`/admin/vendors/${id}/block`),
};

export const productsApi = {
  getAll: (params) => apiClient.get('/admin/products', { params }),
  getOne: (id) => apiClient.get(`/admin/products/${id}`),
  approve: (id) => apiClient.post(`/admin/products/${id}/approve`),
  reject: (id) => apiClient.post(`/admin/products/${id}/reject`),
};

export const ordersApi = {
  getAll: (params) => apiClient.get('/admin/orders', { params }),
  getOne: (id) => apiClient.get(`/admin/orders/${id}`),
};
