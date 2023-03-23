<?php

namespace App;

class FrontFaq extends BaseModel
{
    protected $guarded = ['id'];

    public function language()
    {
        return $this->belongsTo(LanguageSetting::class, 'language_setting_id');
    }

}
