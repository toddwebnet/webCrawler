<?php

namespace App\Models;

class Html extends ValidModel
{
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
