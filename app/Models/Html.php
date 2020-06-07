<?php

namespace App\Models;

class Html extends ValidModel
{
    public const UNPROCESSED = 0;
    public const FLAGGED = 1;
    public const PROCESSED = 2;

    protected $fillable = ['url_id', 'html'];

    public function setHtmlAttribute($value)
    {
        $this->attributes['html'] = utf8_encode($value);
    }

    public function getHtmlAttribute($value)
    {
        return utf8_decode($value);
    }
}
