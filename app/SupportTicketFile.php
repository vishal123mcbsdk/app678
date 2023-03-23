<?php

namespace App;

class SupportTicketFile extends BaseModel
{

    protected $appends = ['file_url', 'icon'];

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('support-ticket-files/' . $this->support_ticket_reply_id . '/' . $this->hashname);
    }

}
