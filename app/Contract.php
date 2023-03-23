<?php

namespace App;

use App\Observers\ContractObserver;
use App\Scopes\CompanyScope;

class Contract extends BaseModel
{
    protected $dates = [
        'start_date',
        'end_date'
    ];
    protected $appends = ['image_url'];
    
    public function getImageUrlAttribute()
    {
        return ($this->company_logo) ? asset_url('avatar/' . $this->company_logo) : asset('img/default-profile-3.png');
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id')->withoutGlobalScopes(['active', CompanyScope::class]);
    }

    public function contract_type()
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id');
    }

    public function signature()
    {
        return $this->hasOne(ContractSign::class, 'contract_id');
    }

    public function discussion()
    {
        return $this->hasMany(ContractDiscussion::class);
    }

    public function renew_history()
    {
        return $this->hasMany(ContractRenew::class, 'contract_id');
    }

    public function files()
    {
        return $this->hasMany(ContractFile::class, 'contract_id')->orderBy('id', 'desc');
    }

}
