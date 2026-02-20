# Business Networking & Partnership Module

## Overview

B2B networking for LocalSaver: discovery, public profiles, partnerships, joint coupons, and customer overlap intelligence.

## 1. Business Discovery

- **Route**: `/businesses`
- **Service**: `BusinessDiscoveryService`
- Nearby businesses by location + category
- Trending businesses (popularity_score + recent redemptions)
- Customer overlap suggestions (businesses with shared users)

## 2. Public Business Profile

- **Route**: `/businesses/{business}`
- Shows: name, category, address, popularity_score, total redemptions
- Active coupons (single + joint)
- Partner businesses list
- "Customers Also Visit" section

## 3. Partnership System

- **Table**: `partnership_requests`, `business_partners`
- Send request → Accept/Reject → Stored in `business_partners`
- Features:
  - Joint coupons (2 businesses linked)
  - Cross-promotion (coupon appears for both)
  - Partner badge on profile
- **Route**: `/business/partnerships`

## 4. Customer Overlap Intelligence

- **Service**: `BusinessNetworkAnalyticsService`
- Detects shared users between businesses
- Dashboard: "Customers also visit"
- Partnership opportunities (high overlap, not yet partnered)
- `getOverlapSuggestions()`, `getPartnershipOpportunities()`

## 5. Joint Coupon Logic

- `partner_business_id` on `coupons` (nullable)
- Rules:
  - Appears in both businesses' profiles
  - Redemption count shared (single record)
  - Radius intersection: user must be within BOTH businesses' radii to redeem
- Create joint coupon: select partner in create form (partners only)

## 6. Admin Control

- **Route**: `/admin/partnerships`
- View all partnerships
- Remove spam partnerships
- Monitor joint coupons list

## Migrations

```bash
php artisan migrate
```

- `partnership_requests`
- `business_partners`
- `coupons.partner_business_id`
- `businesses.popularity_score`

## Popularity Score

Run daily: `php artisan businesses:update-popularity`

Formula: sum(used_count) + view_count/10
