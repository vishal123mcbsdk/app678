<?php

namespace App;

use App\Observers\TaskBoardColumnObserver;
use App\Scopes\CompanyScope;

class TaskboardColumn extends BaseModel
{
    protected $fillable = ['column_name', 'slug', 'label_color', 'priority'];

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskBoardColumnObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'board_column_id')->orderBy('column_priority');
    }

    public function membertasks()
    {
        return $this->hasMany(Task::class, 'board_column_id')->where('user_id', auth()->user()->id)->orderBy('column_priority');
    }

    public static function completeColumn()
    {
        return cache()->remember(
            'taskboard-complete',
            60 * 60 * 24,
            function () {
                return TaskboardColumn::where('slug', 'completed')->first();
            }
        );
    }

}
