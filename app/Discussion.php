<?php

namespace App;

use App\Observers\DiscussionObserver;
use App\Scopes\CompanyScope;

class Discussion extends BaseModel
{

    protected static function boot()
    {
        parent::boot();
        static::observe(DiscussionObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    protected $guarded = ['id'];
    protected $dates = ['last_reply_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function last_reply_by()
    {
        return $this->belongsTo(User::class, 'last_reply_by_id');
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_id');
    }

    public function category()
    {
        return $this->belongsTo(DiscussionCategory::class, 'discussion_category_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function files()
    {
        return $this->hasMany(DiscussionFile::class, 'discussion_id');
    }

}
