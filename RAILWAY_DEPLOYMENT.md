# Railway Deployment Guide for Laravel App

## Step 1: Prepare Your Repository

1. Make sure all files are committed to Git:
   ```bash
   git add .
   git commit -m "Prepare for Railway deployment"
   git push
   ```

## Step 2: Create Railway Account

1. Go to https://railway.app
2. Sign up with GitHub (recommended)
3. Authorize Railway to access your repositories

## Step 3: Create New Project

1. Click "New Project"
2. Select "Deploy from GitHub repo"
3. Choose your Laravel repository
4. Railway will auto-detect it's a PHP project

## Step 4: Add MySQL Database

1. In your project, click "New"
2. Select "Database" → "Add MySQL"
3. Railway will create a MySQL instance
4. Note: Database credentials are auto-injected as environment variables

## Step 5: Configure Environment Variables

Click on your web service → "Variables" tab, add these:

### Required Variables:
```
APP_NAME=YourAppName
APP_ENV=production
APP_KEY=                    # Generate this - see below
APP_DEBUG=false
APP_URL=https://your-app.railway.app

# Database (Railway auto-provides these, but verify):
DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail (use Brevo - see below)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-email
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# OAuth (add your credentials)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-secret
GOOGLE_REDIRECT_URL=${APP_URL}/auth/google/callback
```

### Generate APP_KEY:
Run locally:
```bash
php artisan key:generate --show
```
Copy the output and paste it as APP_KEY value in Railway.

## Step 6: Add Worker Service (for Queue Jobs)

1. In your project, click "New" → "Empty Service"
2. Connect to same GitHub repo
3. In Settings → "Start Command", enter:
   ```
   php artisan queue:work --tries=3 --timeout=90
   ```
4. Share the same environment variables with web service

## Step 7: Run Migrations

After first deployment:

1. Go to your web service
2. Click "Deployments" tab
3. Click on the latest deployment
4. Open "View Logs"
5. Once deployed, go to "Settings" → "Deploy"
6. Add a deploy command (one-time):
   ```
   php artisan migrate --force
   ```

Or use Railway CLI:
```bash
railway run php artisan migrate --force
```

## Step 8: Set Up Email (Brevo)

1. Go to https://www.brevo.com
2. Sign up for free account
3. Go to "SMTP & API" → "SMTP"
4. Get your SMTP credentials
5. Add to Railway environment variables (see Step 5)

## Step 9: Configure OAuth Callbacks

Once deployed, you'll get a URL like: `https://your-app.railway.app`

### Google OAuth:
1. Go to https://console.cloud.google.com
2. Create project → Enable Google+ API
3. Create OAuth credentials
4. Add authorized redirect URI:
   ```
   https://your-app.railway.app/auth/google/callback
   ```
5. Copy Client ID and Secret to Railway env vars

### Facebook OAuth (if needed):
1. Go to https://developers.facebook.com
2. Create app
3. Add redirect URI: `https://your-app.railway.app/auth/facebook/callback`
4. Copy credentials to Railway

## Step 10: Storage Setup

For file uploads (application documents), you need persistent storage:

**Option A: Use Railway Volumes**
1. In web service → "Settings" → "Volumes"
2. Add volume: `/app/storage/app/public`

**Option B: Use S3 (recommended for production)**
1. Sign up for AWS S3 or Cloudflare R2 (free tier)
2. Update `.env`:
   ```
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=your-key
   AWS_SECRET_ACCESS_KEY=your-secret
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your-bucket
   ```

## Step 11: Set Up Scheduler (for Feedback Reminders)

Your app has `SendFeedbackReminders` command. Set up cron:

1. In Railway, add another service (or use worker)
2. Start command:
   ```
   while true; do php artisan schedule:run; sleep 60; done
   ```

Or use Railway Cron (if available in your plan).

## Troubleshooting

### Build fails:
- Check logs in Railway dashboard
- Ensure `composer.json` and `package.json` are correct
- Verify PHP version (8.2)

### Database connection fails:
- Verify MySQL service is running
- Check environment variables are linked
- Ensure `DB_HOST` uses Railway's internal URL

### 500 errors:
- Check logs: Railway dashboard → Deployments → View Logs
- Verify APP_KEY is set
- Run `php artisan config:clear` via Railway CLI

### Queue jobs not running:
- Ensure worker service is deployed
- Check worker logs
- Verify QUEUE_CONNECTION=database

## Useful Railway CLI Commands

Install CLI:
```bash
npm i -g @railway/cli
```

Login:
```bash
railway login
```

Link project:
```bash
railway link
```

Run commands:
```bash
railway run php artisan migrate
railway run php artisan db:seed
railway run php artisan queue:work
```

View logs:
```bash
railway logs
```

## Cost Estimate (Free Tier)

- $5 free credits/month
- Web service: ~$2-3/month
- MySQL: ~$1-2/month
- Worker: ~$1/month
- Total: ~$4-6/month (covered by free credits)

## Next Steps After Deployment

1. Test all features (registration, login, OAuth)
2. Test email notifications
3. Test file uploads
4. Test queue jobs (feedback reminders)
5. Set up custom domain (optional)
6. Enable HTTPS (automatic on Railway)

## Support

- Railway Docs: https://docs.railway.app
- Railway Discord: https://discord.gg/railway
- Laravel Docs: https://laravel.com/docs
