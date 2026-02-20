<x-app-layout title="Privacy Policy">
    <div class="max-w-3xl mx-auto py-12 prose prose-invert">
        <h1>Privacy Policy</h1>
        <p class="text-white/60">Last updated: {{ now()->format('F j, Y') }}</p>
        <h2>1. Data We Collect</h2>
        <p>We collect: name, phone/email, location (with consent), redemption history, and usage data to personalise your experience.</p>
        <h2>2. How We Use It</h2>
        <p>To show relevant coupons, process redemptions, send notifications (with your consent), improve the service, and prevent fraud.</p>
        <h2>3. Sharing</h2>
        <p>We share data with merchants whose coupons you redeem. We do not sell your data to third parties.</p>
        <h2>4. Security</h2>
        <p>We use encryption, secure storage, and access controls to protect your data.</p>
        <h2>5. Your Rights</h2>
        <p>You may request access, correction, or deletion of your data. Contact us to exercise these rights.</p>
        <h2>6. Cookies</h2>
        <p>We use session cookies for authentication and essential functionality.</p>
        <p><a href="{{ route('home') }}" class="text-violet-400">← Back</a></p>
    </div>
</x-app-layout>
