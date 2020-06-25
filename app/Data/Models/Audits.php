<?php

namespace App\Data\Models;

class Audit extends Model
{
    protected $with = ['user'];

    protected $dates = ['created_at'];

    protected $appends = ['formatted_created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i:s')
            : null;
    }
}