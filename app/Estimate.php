<?php

namespace App;

use App\Observers\EstimateObserver;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Estimate extends BaseModel
{
    use Notifiable;
    use CustomFieldsTrait;

    protected $dates = ['valid_till'];
    protected $appends = ['total_amount', 'valid_date', 'estimate_number', 'original_estimate_number'];

    protected static function boot()
    {
        parent::boot();

        static::observe(EstimateObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function items()
    {
        return $this->hasMany(EstimateItem::class, 'estimate_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes(['active']);
    }

    public function client_detail()
    {
        return $this->hasOne(ClientDetails::class, 'user_id', 'client_id')->withoutGlobalScopes(['company'])->where('company_id', $this->company_id);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withoutGlobalScopes(['enable']);
    }

    public function sign()
    {
        return $this->hasOne(AcceptEstimate::class, 'estimate_id');
    }

    public function getTotalAmountAttribute()
    {

        if (!is_null($this->total) && !is_null($this->currency_symbol)) {
            return $this->currency_symbol . $this->total;
        }

        return '';
    }

    public function getValidDateAttribute()
    {
        if (!is_null($this->valid_till)) {
            return Carbon::parse($this->valid_till)->format('d F, Y');
        }
        return '';
    }

    public function getOriginalEstimateNumberAttribute()
    {
        $invoiceSettings = InvoiceSetting::select('estimate_digit')->first();
        $zero = '';
        if (strlen($this->estimate_number) < $invoiceSettings->estimate_digit) {
            for ($i = 0; $i < $invoiceSettings->estimate_digit - strlen($this->estimate_number); $i++) {
                $zero = '0' . $zero;
            }
        }
        $zero = $zero . $this->estimate_number;
        return $zero;
    }

    public function getEstimateNumberAttribute($value)
    {
        if (!is_null($value)) {
            $invoiceSettings = InvoiceSetting::select('estimate_prefix', 'estimate_digit')->first();
            $zero = '';
            if (strlen($value) < $invoiceSettings->estimate_digit) {
                for ($i = 0; $i < $invoiceSettings->estimate_digit - strlen($value); $i++) {
                    $zero = '0' . $zero;
                }
            }
            $zero = $invoiceSettings->estimate_prefix . '#' . $zero . $value;
            return $zero;
        }
        return '';
    }

    public static function lastEstimateNumber()
    {
        $invoice = DB::select('SELECT MAX(CAST(`estimate_number` as UNSIGNED)) as estimate_number FROM `estimates` where company_id = "' . company()->id . '"');
        return $invoice[0]->estimate_number;
    }

    public function activeTemplate()
    {
        $invoiceSetting = InvoiceSetting::first();
        $fileName = explode('-', $invoiceSetting->template);
        return 'estimate-'.$fileName[1];
    }

}
