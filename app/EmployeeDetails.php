<?php

namespace App;

use App\Observers\EmployeeDetailObserver;
use App\Scopes\CompanyScope;
use App\Traits\CustomFieldsTrait;

class EmployeeDetails extends BaseModel
{
    use CustomFieldsTrait;

    protected $table = 'employee_details';

    protected $dates = ['joining_date', 'last_date'];

    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeDetailObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(Team::class, 'department_id');
    }

}
