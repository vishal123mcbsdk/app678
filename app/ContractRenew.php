<?php

namespace App;

use App\Observers\ContractRenewObserver;
use App\Scopes\CompanyScope;

class ContractRenew extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractRenewObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    protected $dates = [
        'start_date',
        'end_date'
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function renewedBy()
    {
        return $this->belongsTo(User::class, 'renewed_by');
    }

}
