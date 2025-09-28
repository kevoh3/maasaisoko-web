# SasaPay Callback URL Setup Guide

## Quick Testing Setup (5 minutes)

### Option 1: Using ngrok (Recommended)

1. **Install ngrok:**
   ```bash
   # Download from https://ngrok.com/download
   # Or use package manager:
   brew install ngrok  # macOS
   choco install ngrok  # Windows
   ```

2. **Start your Laravel app:**
   ```bash
   cd /path/to/your/maasaisoko-web
   php artisan serve
   ```

3. **Create public tunnel:**
   ```bash
   ngrok http 8000
   ```

4. **Copy the HTTPS URL** (e.g., `https://abc123.ngrok.io`)

5. **Set your callback URL in SasaPay settings:**
   ```
   https://abc123.ngrok.io/sasapay/callback
   ```

### Option 2: Using localtunnel (Alternative)

1. **Install localtunnel:**
   ```bash
   npm install -g localtunnel
   ```

2. **Start your Laravel app:**
   ```bash
   php artisan serve
   ```

3. **Create tunnel:**
   ```bash
   lt --port 8000 --subdomain your-app-name
   ```

4. **Use callback URL:**
   ```
   https://your-app-name.loca.lt/sasapay/callback
   ```

## Production Setup

### For Production Deployment:

1. **Get a domain name** (e.g., maasaisoko.com)
2. **Set up hosting** (DigitalOcean, AWS, etc.)
3. **Configure SSL certificate**
4. **Set callback URL:**
   ```
   https://maasaisoko.com/sasapay/callback
   ```

## Environment Configuration

Add to your `.env` file:

```env
# For testing with ngrok
SASAPAY_SANDBOX_CALLBACK_URL=https://abc123.ngrok.io/sasapay/callback

# For production
SASAPAY_LIVE_CALLBACK_URL=https://yourdomain.com/sasapay/callback
```

## Testing the Callback

1. **Test the callback endpoint:**
   ```bash
   curl -X POST https://your-callback-url/sasapay/callback \
        -H "Content-Type: application/json" \
        -d '{"test": "data"}'
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Important Notes

- ✅ **HTTPS Required:** SasaPay requires HTTPS for callbacks
- ✅ **Public URL Required:** The callback must be accessible from the internet
- ✅ **No Authentication:** The callback endpoint should be publicly accessible
- ✅ **CSRF Exempt:** The callback route is already configured to bypass CSRF

## Troubleshooting

### If callback is not working:

1. **Check if URL is accessible:**
   ```bash
   curl -I https://your-callback-url/sasapay/callback
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify route exists:**
   ```bash
   php artisan route:list | grep sasapay
   ```

4. **Test with SasaPay webhook testing tools** (if available)
