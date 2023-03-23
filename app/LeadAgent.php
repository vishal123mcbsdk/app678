<?php

namespace App;

use App\Observers\LeadAgentObserver;
use App\Scopes\CompanyScope;

class LeadAgent extends BaseModel
{
    protected $table = 'lead_agents';

    protected static function boot()
    {
        parent::boot();

        static::observe(LeadAgentObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScope('active');
    }

    public function lead()
    {
        return $this->hasOne(Lead::class);
    }

}
