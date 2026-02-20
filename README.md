# LocalSaver

A location-based coupon platform – discover and redeem nearby deals. Mobile-first PWA with glassmorphism UI.

## Features

- **Users**: Phone OTP / Email login, discover nearby coupons, redeem with one tap, wallet & savings tracker, VIP after 3 redemptions, referral codes
- **Businesses**: ₹199 one-time activation (Razorpay), dashboard (views, redemptions), create/manage coupons, category filters, radius selection
- **Admin**: Approve businesses, manage coupons, view revenue, user stats

## Tech Stack

- Laravel 11, PHP 8.2+
- Blade + Tailwind CSS + Alpine.js
- Razorpay, Google Maps
- PWA (offline fallback, install prompt)

## Installation

```bash
# Clone & install
composer install
cp .env.example .env
php artisan key:generate

# Configure database in .env
php artisan migrate

# Seed admin (email: admin@localsaver.com, password: password)
php artisan db:seed --class=DatabaseSeeder

# Frontend
npm install
npm run dev

# Run app
php artisan serve
```

## Environment

| Key | Description |
|-----|-------------|
| `RAZORPAY_KEY` / `RAZORPAY_SECRET` | Payment (₹199 activation) |
| `GOOGLE_MAPS_API_KEY` | Location & directions |
| `FIREBASE_SERVER_KEY` | Push notifications (optional) |

## OTP (Development)

In local env, OTP codes are logged to `storage/logs/laravel.log`. For production, integrate MSG91, Twilio, or similar in `OtpService::sendOtp()`.

## License

MIT
