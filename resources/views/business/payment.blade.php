<x-app-layout title="Activate Business">
    <div class="max-w-md mx-auto py-12">
        <div class="glass-card p-8 text-center">
            <h2 class="text-2xl font-bold mb-2">Activate Your Business</h2>
            <p class="text-white/60 mb-6">One-time payment of ₹199 to unlock your dashboard</p>
            <p class="text-3xl font-bold text-emerald-400 mb-8">₹199</p>
            <button id="rzp-button" class="w-full btn-glow py-4">Pay with Razorpay</button>
        </div>
    </div>

    @push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        const options = {
            key: "{{ config('services.razorpay.key') }}",
            amount: {{ $amount ?? 19900 }},
            currency: "INR",
            name: "LocalSaver",
            description: "Business Activation",
            order_id: "{{ $order_id }}",
            handler: function (response) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('business.payment.verify') }}";
                form.innerHTML = `
                    @csrf
                    <input name="razorpay_order_id" value="${response.razorpay_order_id}">
                    <input name="razorpay_payment_id" value="${response.razorpay_payment_id}">
                    <input name="razorpay_signature" value="${response.razorpay_signature}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        };
        const rzp = new Razorpay(options);
        document.getElementById('rzp-button').onclick = () => rzp.open();
    </script>
    @endpush
</x-app-layout>
