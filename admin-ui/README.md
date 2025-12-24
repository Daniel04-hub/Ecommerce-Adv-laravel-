# React Admin Dashboard

Modern React-based admin dashboard for the e-commerce platform.

## Tech Stack

- **React 18** - UI library
- **React Router 6** - Client-side routing
- **Axios** - HTTP client for API calls
- **Tailwind CSS** - Styling
- **Vite** - Build tool and dev server

## Features

### Pages
- **Dashboard** - Statistics and overview
- **Users** - User management
- **Vendors** - Vendor approval and management
- **Products** - Product approval and monitoring
- **Orders** - Order monitoring

### Authentication
- Uses existing Laravel Sanctum authentication
- Token stored in localStorage
- Automatic redirect to login if not authenticated
- Admin-only access via role middleware

## Development Setup

### Install Dependencies

```bash
cd admin-ui
npm install
```

### Run Development Server

```bash
npm run dev
```

The React app will run on `http://localhost:3001`

### Build for Production

```bash
npm run build
```

Builds to `../public/admin-ui-dist/`

## API Integration

### Base URL
All API calls proxy to Laravel backend at `http://localhost:8000`

### Authentication Flow

1. Admin logs in via Laravel `/login`
2. On successful login, store Sanctum token in localStorage:
   ```javascript
   localStorage.setItem('admin_token', token);
   ```
3. Token automatically included in all API requests via Axios interceptor

### Available API Endpoints

All endpoints require authentication and admin role.

**Dashboard:**
- `GET /api/admin/dashboard/stats` - Get dashboard statistics

**Users:**
- `GET /api/admin/users` - List all users
- `GET /api/admin/users/{id}` - Get user details

**Vendors:**
- `GET /api/admin/vendors` - List all vendors
- `PATCH /api/admin/vendors/{id}/approve` - Approve vendor
- `PATCH /api/admin/vendors/{id}/block` - Block vendor

**Products:**
- `GET /api/admin/products` - List all products
- `GET /api/admin/products/{id}` - Get product details
- `POST /api/admin/products/{id}/approve` - Approve product
- `POST /api/admin/products/{id}/reject` - Reject product

**Orders:**
- `GET /api/admin/orders` - List all orders
- `GET /api/admin/orders/{id}` - Get order details

## Project Structure

```
admin-ui/
├── src/
│   ├── api/
│   │   └── client.js          # Axios client & API functions
│   ├── components/
│   │   └── Layout.jsx         # Main layout with sidebar
│   ├── hooks/
│   │   └── useAuth.js         # Authentication hook
│   ├── pages/
│   │   ├── Dashboard.jsx      # Dashboard page
│   │   ├── Users.jsx          # Users management
│   │   ├── Vendors.jsx        # Vendors management
│   │   ├── Products.jsx       # Products management
│   │   └── Orders.jsx         # Orders monitoring
│   ├── App.jsx                # Main app component
│   ├── main.jsx               # React entry point
│   └── index.css              # Global styles
├── index.html                 # HTML template
├── package.json               # Dependencies
├── vite.config.js             # Vite configuration
├── tailwind.config.js         # Tailwind configuration
└── postcss.config.js          # PostCSS configuration
```

## Important Notes

### Blade Admin vs React Admin

- **Blade Admin:** `/admin/dashboard` - Existing Blade-based admin UI
- **React Admin:** Runs separately on port 3001 during development
- Both are available - admins can choose which to use
- Customer and Vendor interfaces remain Blade-based (unchanged)

### Authentication Token

The React app expects a token in localStorage:

```javascript
// After successful Laravel login, set token:
localStorage.setItem('admin_token', yourSanctumToken);

// Then redirect to React app
window.location.href = 'http://localhost:3001';
```

### CORS Configuration

Ensure Laravel's CORS config allows requests from React dev server:

```php
// config/cors.php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3001'],
'supports_credentials' => true,
```

## Customization

### Add New Pages

1. Create new page component in `src/pages/`
2. Add route in `src/App.jsx`
3. Add navigation link in `src/components/Layout.jsx`
4. Create corresponding API controller in Laravel

### Styling

Uses Tailwind CSS utility classes. Customize in:
- `tailwind.config.js` - Theme configuration
- `src/index.css` - Global styles

### API Client

All API functions in `src/api/client.js`:
- Add new endpoints by extending existing API objects
- Interceptors handle auth token and 401 responses

## Production Deployment

### Option 1: Separate React App
Keep React app separate, deploy to different domain/port

### Option 2: Build to Laravel Public
Build React app and serve from Laravel:

```bash
npm run build
```

Then configure Laravel route to serve the built files from `public/admin-ui-dist/`

### Environment Variables

Create `.env.local` for environment-specific config:

```env
VITE_API_BASE_URL=https://your-api-domain.com
```

Update `src/api/client.js` to use:

```javascript
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
});
```

## Security

- ✅ Admin-only API access via Sanctum middleware
- ✅ Role-based authorization on all endpoints
- ✅ Token-based authentication
- ✅ Automatic logout on 401 response
- ✅ No sensitive data in frontend code

## Troubleshooting

### 401 Unauthorized
- Ensure token is stored correctly in localStorage
- Verify user has admin role
- Check token hasn't expired

### CORS Errors
- Configure Laravel CORS settings
- Ensure credentials are included in requests

### API Not Found
- Verify Laravel API routes are registered
- Check route:list for API routes
- Ensure bootstrap/app.php includes api routes

---

**Created:** December 23, 2025  
**React Version:** 18.3.1  
**Build Tool:** Vite 5.4.2
