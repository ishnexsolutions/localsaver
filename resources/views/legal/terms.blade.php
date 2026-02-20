<x-app-layout title="Terms & Conditions">
    <div class="max-w-3xl mx-auto py-12 prose prose-invert">
        <h1>Terms & Conditions</h1>
        <p class="text-white/60">Last updated: {{ now()->format('F j, Y') }}</p>
        <h2>1. Acceptance</h2>
        <p>By using LocalSaver, you agree to these Terms. If you disagree, do not use the service.</p>
        <h2>2. Service Description</h2>
        <p>LocalSaver is a location-based coupon platform connecting users with local business offers. We do not guarantee coupon availability, merchant compliance, or redemption success.</p>
        <h2>3. User Responsibilities</h2>
        <p>Users must provide accurate information, use coupons per merchant rules, and not abuse the system. Fraudulent activity may result in account suspension.</p>
        <h2>4. Business Responsibilities</h2>
        <p>Businesses must honour redeemed coupons, maintain accurate offers, and comply with applicable laws. Failure may result in removal from the platform.</p>
        <h2>5. Limitation of Liability</h2>
        <p>LocalSaver is not liable for merchant disputes, coupon validity, or indirect damages. Use at your own risk.</p>
        <h2>6. Changes</h2>
        <p>We may modify these Terms. Continued use constitutes acceptance.</p>
        <p><a href="{{ route('home') }}" class="text-violet-400">← Back</a></p>
    </div>
</x-app-layout>
