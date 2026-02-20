<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessDiscoveryController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PartnershipController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('home'));
Route::get('/up', fn () => response()->json(['status' => 'ok']));
Route::get('/offline', fn () => view('offline'))->name('offline');
Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/coupon-rules', [\App\Http\Controllers\LegalController::class, 'couponRules'])->name('legal.coupon-rules');
Route::get('/refund-policy', [\App\Http\Controllers\LegalController::class, 'refundPolicy'])->name('legal.refund');
Route::get('/business-authenticity', [\App\Http\Controllers\LegalController::class, 'businessAuthenticity'])->name('legal.authenticity');
Route::post('/webhooks/razorpay', [\App\Http\Controllers\RazorpayWebhookController::class, 'handle'])->name('webhooks.razorpay');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/location', [HomeController::class, 'updateLocation'])->name('location.update');

Route::get('/businesses', [BusinessDiscoveryController::class, 'index'])->name('businesses.index');
Route::get('/businesses/{business}', [BusinessProfileController::class, 'show'])->name('businesses.show');
Route::post('/businesses/{business}/rate', [BusinessProfileController::class, 'rate'])->name('businesses.rate')->middleware('auth')->middleware('throttle:10,1');

Route::get('/coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');

Route::middleware('auth')->group(function () {
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'userSteps'])->name('onboarding.user');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'completeUserStep'])->name('onboarding.user.complete');
    Route::post('/coupons/{coupon}/redeem', [CouponController::class, 'redeem'])->name('coupons.redeem')->middleware('throttle:20,1');
    Route::post('/coupons/{coupon}/complain', [CouponController::class, 'complain'])->name('coupons.complain')->middleware('throttle:5,1');
    Route::post('/coupons/{coupon}/share', [CouponController::class, 'share'])->name('coupons.share')->middleware('throttle:20,1');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/otp', [AuthController::class, 'sendOtp'])->name('login.otp')->middleware('throttle:10,1');
Route::post('/login/verify', [AuthController::class, 'verifyOtp'])->name('login.verify')->middleware('throttle:20,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::get('/business/register', [AuthController::class, 'showBusinessRegister'])->name('business.register');
Route::post('/business/register', [AuthController::class, 'registerBusiness'])->name('business.register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:business'])->prefix('business')->name('business.')->group(function () {
    Route::get('/dashboard', [BusinessController::class, 'dashboard'])->name('dashboard');
    Route::get('/payment', [BusinessController::class, 'paymentForm'])->name('payment');
    Route::post('/payment/verify', [BusinessController::class, 'verifyPayment'])->name('payment.verify')->middleware('throttle:30,1');
    Route::get('/coupons/create', [BusinessController::class, 'createCouponForm'])->name('coupons.create');
    Route::post('/coupons', [BusinessController::class, 'storeCoupon'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [BusinessController::class, 'editCoupon'])->name('coupons.edit');
    Route::put('/coupons/{coupon}', [BusinessController::class, 'updateCoupon'])->name('coupons.update');
    Route::post('/coupons/{coupon}/delete', [BusinessController::class, 'destroyCoupon'])->name('coupons.destroy');
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'businessSteps'])->name('onboarding.business');
    Route::post('/onboarding/complete', [\App\Http\Controllers\OnboardingController::class, 'completeBusinessStep'])->name('onboarding.business.complete');
    Route::get('/partnerships', [PartnershipController::class, 'index'])->name('partnerships.index');
    Route::post('/partnerships/request', [PartnershipController::class, 'sendRequest'])->name('partnerships.request');
    Route::post('/partnerships/{partnershipRequest}/accept', [PartnershipController::class, 'acceptRequest'])->name('partnerships.accept');
    Route::post('/partnerships/{partnershipRequest}/reject', [PartnershipController::class, 'rejectRequest'])->name('partnerships.reject');
    Route::post('/partnerships/remove/{partner}', [PartnershipController::class, 'removePartner'])->name('partnerships.remove');
});

Route::middleware(['auth', 'role:admin'])->prefix('founder')->name('founder.')->group(function () {
    Route::get('/', [\App\Http\Controllers\FounderController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/businesses', [AdminController::class, 'businesses'])->name('businesses');
    Route::post('/businesses/{business}/approve', [AdminController::class, 'approveBusiness'])->name('businesses.approve');
    Route::post('/businesses/{business}/reject', [AdminController::class, 'rejectBusiness'])->name('businesses.reject');
    Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
    Route::post('/coupons/{coupon}/delete', [AdminController::class, 'deleteCoupon'])->name('coupons.delete');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');
    Route::get('/fraud', [AdminController::class, 'fraud'])->name('fraud');
    Route::post('/fraud/{flag}/review', [AdminController::class, 'reviewFraud'])->name('fraud.review');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminController::class, 'unsuspendUser'])->name('users.unsuspend');
    Route::get('/metrics', [AdminController::class, 'metrics'])->name('metrics');
    Route::get('/partnerships', [AdminController::class, 'partnerships'])->name('partnerships');
    Route::post('/partnerships/{businessPartner}/remove', [AdminController::class, 'removePartnership'])->name('partnerships.remove');
    Route::get('/health', [\App\Http\Controllers\AdminHealthController::class, 'index'])->name('health');
});
