# LocalSaver Production Readiness Checklist

## Completed Features

### 1. User Onboarding
- Location detect & confirm, interests (Food, Salon, Health, Services, Shopping)
- Potential savings nearby, wallet visual, preferences stored
- `preferred_categories`, `onboarding_completed` on users

### 2. Business Onboarding
- Complete profile, first coupon (guided), estimated reach, boost encouragement
- `onboarding_completed` on businesses

### 3. Coupon Quality & Trust
- **CouponQualityService**: score from redemption rate, discount strength, expiry discipline, rating, complaints
- Low score → feed penalty applied in **CouponRankingService**
- Cache invalidation on coupon create/update/delete, redemption

### 4. Business Rating & Trust
- User ratings (1–5) on business profile
- **BusinessTrustService**: trust score from ratings, verified badge
- `business_ratings`, `business_trust_scores` tables

### 5. Notification Intelligence
- **RetentionService**: time-based, distance-trigger, flash deal, silent hours
- **Notification preferences UI** in Profile: daily_deals, flash_deals, milestones, comeback, silent hours
- All notifications queued

### 6. Founder Analytics
- `/founder` dashboard: DAU, WAU, new users, redemptions, active businesses, top coupon, revenue, conversion, retention, drop-off funnel
- `analytics_daily` populated by `analytics:snapshot` (daily 23:55)

### 7. Legal Pages
- Terms, Privacy, Coupon Rules, Refund Policy, Business Authenticity
- Footer links in all pages

### 8. Stability
- Redemption in `DB::transaction`, double-redemption check
- **Razorpay webhook**: signature verification, idempotent `completePaymentFromWebhook`
- CSRF exempt: `webhooks/razorpay`
- Queue workers, scheduler, coupon expiry commands
- Push notification job: 3 retries, 60s backoff

### 9. Performance
- DB indexes: `coupons(expiry_date)`, `businesses(lat,lng)`, `redemptions(user_id,coupon_id)`
- Feed & popularity caching (5 min TTL)
- Cache invalidation on update/expiry

### 10. Growth Hooks
- Redeem 5 → reward flag (displayed on profile)
- Founding member badge (≤1000 users, shown on profile)
- Share coupon button → unlocks hidden deal (Web Share API or copy)
- Loyalty streak (RetentionService, shown on profile)

### 11. Launch Safety
- **Admin Health** (`/admin/health`): empty categories, weak coupons, inactive businesses, fraud alerts, system health (DB, cache, queue)

### 12. Security
- Rate limiting: login OTP (10/min), verify (20/min), redeem (20/min), rate (10/min), complain (5/min), payment (30/min)
- Input validation, CSRF, role-based access

---

## Deploy Checklist

1. **Environment**
   - `RAZORPAY_WEBHOOK_SECRET` (required for webhook)
   - `CACHE_STORE=redis` for production (optional, file works)
   - `QUEUE_CONNECTION=redis` or `database` with workers

2. **Cron**
   ```bash
   * * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Queue Workers**
   ```bash
   php artisan queue:work --tries=3
   ```

4. **Migrations**
   ```bash
   php artisan migrate --force
   ```

5. **Razorpay Webhook**
   - URL: `https://yourdomain.com/webhooks/razorpay`
   - Event: `payment.captured`
