<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function terms()
    {
        return view('legal.terms');
    }

    public function privacy()
    {
        return view('legal.privacy');
    }

    public function couponRules()
    {
        return view('legal.coupon-rules');
    }

    public function refundPolicy()
    {
        return view('legal.refund-policy');
    }

    public function businessAuthenticity()
    {
        return view('legal.business-authenticity');
    }
}
