# LocalSaver Post-MVP Upgrade Summary

## 1. Smart Feed Ranking (CouponRankingService)

- **Score formula**: Boosted (100+) > Distance (30) > Expiry urgency (25) > Popularity (20) > User preference (25)
- Personalised feed for logged-in users (category preferences from past redemptions)
- Fallback generic ranking for guests
- Popularity cached per coupon (1h TTL)
- Feed cache: 5 min TTL

## 2. Retention & Habit Engine (RetentionService)

- **Daily Deals**: `retention:daily` at 10:00 – push top 3 nearby deals to users with location + push subscription
- **Inactive (7 days)**: Comeback notification
- **Savings Milestones**: ₹100, ₹500, ₹1000, ₹5000 – push on crossing
- **Redemption Streak**: Weekly streak; VIP unlock after 3 consecutive weeks
- Uses `SendPushNotificationJob` (queued) + Firebase

## 3. Business Analytics (BusinessAnalyticsService)

- First-time vs Repeat customers
- Funnel: Views → Clicks → Redemptions (conversion %)
- Peak redemption hour
- Auto suggestion: "Create comeback coupon for inactive users" when 10+ inactive nearby
- Tracks `coupon_clicks` (user_id, ip_hash, session_id, is_unique, converted_to_redemption)

## 4. Fraud Protection (FraudProtectionService)

- Cooldown: 60 min between same coupon redemption
- Daily limit: 10 redemptions per user
- Rapid redemptions: 5+ in 10 min → flag
- Soft log: ip_hash + device_hash
- `fraud_flags` table, Admin fraud panel (Clear / Suspend)

## 5. Referral System (ReferralService)

- `referrals` table (referrer_id, referred_id, vip_unlocked, referred_redemption_count)
- VIP unlock after referred user has 3 redemptions
- Register with `?ref=CODE` or `referral_code` in form

## 6. Boost Engine (BoostService)

- `boost_until`, `featured_flag`, `priority_score` on coupons
- `boost:expire` hourly – auto disable expired boosts
- `getFeaturedCoupons()` for banner slot

## 7. Performance

- Redis-ready: set `CACHE_STORE=redis` in .env
- New indexes: `coupons(boosted_until)`, `redemptions(user_id, created_at)`
- All push notifications queued

## 8. Trending (TrendingService)

- Redemptions in last 24h by area
- "Trending Near You" section on homepage
- Cached 15 min

## 9. Admin Enhancements

- **Fraud panel**: `/admin/fraud` – review flags, suspend users
- **Metrics**: `/admin/metrics` – DAU, WAU, conversion, retention D1/D7/D30
- **Users**: Suspend / Unsuspend

## 10. Analytics Tracking (AnalyticsTrackingService)

- `analytics_daily` table: DAU, WAU, redemptions, revenue, conversion_rate
- `analytics:snapshot` daily at 23:55
- Retention rates (cohort-based)

## New Migrations

- `fraud_flags`
- `referrals`
- `coupon_clicks`
- `analytics_daily`
- Users: `last_active_at`, `redemption_streak_weeks`, `vip_unlocked`, `referral_count`, `milestones_notified`, `suspended`
- Coupons: `featured_flag`, `priority_score`
- Indexes migration

## Scheduler

```bash
# Add to crontab
* * * * * cd /path && php artisan schedule:run
```

## Run Migrations

```bash
php artisan migrate
```

## Queue Worker

```bash
php artisan queue:work
```
