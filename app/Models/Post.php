<?php

namespace App\Models;

class Post extends Model
{
    protected $fillable = array(
        "TITLE", "CONTENT",
    );

    public function COMMENTS()
    {
    	return $this->hasMany("\App\Models\Comment");
    }
}
