<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/verify-ipn',
        '/verify-webhook',
        '/save-invoices',
        '/save-razorpay-invoices',
        '/save-paystack-invoices',
        '/save-authorize-invoices',
        '/*-webhook',
        '/lead-form/leadStore',
        '/payfast-notification',
        '/client-payfast-invoice'
    ];
}
