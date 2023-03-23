<?php
namespace App;

use App\Observers\LeadFileObserver;
use App\Scopes\CompanyScope;

class LeadFiles extends BaseModel
{
    // Don't forget to fill this array
    protected $fillable = [];

    protected $guarded = ['id'];
    protected $table = 'lead_files';

    protected $appends = ['file_url'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('lead-files/'.$this->lead_id.'/'.$this->hashname);
    }

    protected static function boot()
    {
        parent::boot();

        static::observe(LeadFileObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
