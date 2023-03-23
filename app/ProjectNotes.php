<?php

namespace App;

use App\Observers\ProjectNotesObserver;
use App\Scopes\CompanyScope;

class ProjectNotes extends BaseModel
{
    protected $table = 'project_notes';

    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectNotesObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function member()
    {
        return $this->hasMany(ProjectUserNotes::class);
    }

}