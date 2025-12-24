# Laravel Pulse Monitoring Setup Guide

## Overview
Laravel Pulse has been successfully installed to monitor your e-commerce platform's performance and health metrics.

## What's Monitored

### ✅ Enabled Recorders
1. **Queues** - Monitor queue workload and throughput
2. **Jobs** - Track job execution and failures
3. **Slow Jobs** - Identify jobs taking longer than 1 second
4. **Slow Queries** - Database queries exceeding 1 second
5. **Slow Requests** - HTTP requests slower than 1 second
6. **Exceptions** - Application errors and exceptions
7. **Servers** - Server resources and disk usage

### ❌ Disabled Recorders (not needed for basic monitoring)
- Cache Interactions
- Slow Outgoing Requests
- User Jobs
- User Requests

## Access Control

**Pulse Dashboard URL:** `/pulse`

**Authorization:** Admin users only
- Only users with the `admin` role can access the Pulse dashboard
- Configured via Gate in `App\Providers\PulseServiceProvider`
- Unauthorized users will receive a 403 Forbidden response

## Configuration

### Files Created/Modified
- `config/pulse.php` - Pulse configuration
- `app/Providers/PulseServiceProvider.php` - Authorization logic
- `bootstrap/providers.php` - Service provider registration
- `database/migrations/*_create_pulse_tables.php` - Database tables

### Database Tables
Pulse creates the following tables:
- `pulse_aggregates`
- `pulse_entries`
- `pulse_values`

### Data Retention
- **Storage:** 7 days (configurable via `PULSE_STORAGE_KEEP`)
- **Ingest:** 7 days (configurable via `PULSE_INGEST_KEEP`)

## Environment Variables

Add these to your `.env` file if you need to customize:

```env
# Enable/disable Pulse entirely
PULSE_ENABLED=true

# Custom dashboard path (default: pulse)
PULSE_PATH=pulse

# Storage configuration
PULSE_STORAGE_DRIVER=database
PULSE_STORAGE_KEEP="7 days"

# Ingest configuration
PULSE_INGEST_DRIVER=storage
PULSE_INGEST_KEEP="7 days"

# Recorder thresholds (in milliseconds)
PULSE_SLOW_JOBS_THRESHOLD=1000
PULSE_SLOW_QUERIES_THRESHOLD=1000
PULSE_SLOW_REQUESTS_THRESHOLD=1000
```

## Usage

### Accessing the Dashboard
1. Log in as an admin user
2. Navigate to: `https://yourdomain.com/pulse`
3. View real-time metrics and historical data

### What You'll See
- **Usage** - Memory and CPU usage per server
- **Queues** - Jobs processed and pending
- **Slow Jobs** - Jobs exceeding threshold
- **Slow Queries** - Database queries to optimize
- **Slow Requests** - HTTP endpoints to improve
- **Exceptions** - Application errors requiring attention
- **Servers** - System resource usage

## Performance Impact

✅ **Minimal Impact:**
- Pulse uses asynchronous data collection
- Storage operations are batched
- Recorders use sampling (sample_rate: 1 = 100%)
- Pulse tables are automatically trimmed

## Monitoring Best Practices

1. **Check Daily** - Review Pulse dashboard for anomalies
2. **Set Thresholds** - Adjust threshold values based on your app's performance
3. **Act on Slow Queries** - Add indexes or optimize queries flagged by Pulse
4. **Monitor Queues** - Ensure jobs are processing without excessive delays
5. **Review Exceptions** - Fix recurring errors promptly

## Disabling Pulse

If you need to temporarily disable Pulse:

```env
PULSE_ENABLED=false
```

Or disable specific recorders:
```env
PULSE_QUEUES_ENABLED=false
PULSE_SLOW_QUERIES_ENABLED=false
```

## Troubleshooting

### Dashboard Returns 403
- Ensure you're logged in as an admin user
- Check that your user has the 'admin' role:
  ```php
  php artisan tinker
  >>> User::find(1)->hasRole('admin')
  ```

### No Data Showing
- Verify Pulse is enabled: `PULSE_ENABLED=true`
- Check that recorders are enabled in `config/pulse.php`
- Ensure you have traffic/jobs running to generate metrics
- Run: `php artisan pulse:check` to verify configuration

### High Database Load
- Increase trim lottery odds in `config/pulse.php`
- Reduce data retention period
- Consider using Redis as ingest driver for high-traffic sites

## Production Recommendations

For production environments:

1. **Use Redis for Ingest:**
   ```env
   PULSE_INGEST_DRIVER=redis
   PULSE_REDIS_CONNECTION=default
   ```

2. **Adjust Sample Rates:** (sample only 10% of traffic)
   ```env
   PULSE_SLOW_REQUESTS_SAMPLE_RATE=0.1
   PULSE_SLOW_QUERIES_SAMPLE_RATE=0.1
   ```

3. **Run Pulse Work Command:**
   ```bash
   php artisan pulse:work
   ```
   Add this to your supervisor configuration.

4. **Scheduled Trimming:**
   The trim lottery runs automatically, but you can manually trim:
   ```bash
   php artisan pulse:clear
   ```

## Security Notes

- ✅ Admin-only access configured
- ✅ No sensitive data exposed in metrics
- ✅ Pulse tables excluded from monitoring to prevent recursion
- ✅ Dashboard routes protected by authorization middleware

## Next Steps

After testing Pulse:
1. Adjust thresholds based on your baseline performance
2. Set up alerts for critical metrics (requires custom implementation)
3. Consider integrating with external monitoring (e.g., Sentry, Bugsnag)
4. Review and optimize identified slow queries and requests

---

**Installation Date:** December 23, 2025  
**Laravel Version:** 12.x  
**Pulse Version:** 1.4.7
