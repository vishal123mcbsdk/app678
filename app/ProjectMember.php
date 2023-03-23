<?php

namespace App;

use App\Observers\ProjectMemberObserver;
use App\Scopes\CompanyScope;
use Illuminate\Notifications\Notifiable;

class ProjectMember extends BaseModel
{
    use Notifiable;

    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectMemberObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function routeNotificationForMail()
    {
        return $this->user->email;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withoutGlobalScopes(['active']);
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public static function byProject($id)
    {
        return ProjectMember::join('users', 'users.id', '=', 'project_members.user_id')
            ->where('project_members.project_id', $id)
            ->where('users.status', 'active')
            ->get();
    }

    public static function checkIsMember($projectId, $userId)
    {
        return ProjectMember::where('project_id', $projectId)
            ->where('user_id', $userId)->first();
    }

}
