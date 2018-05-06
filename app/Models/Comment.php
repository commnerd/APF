<?php

namespace App\Models;

class Comment extends Model
{
    protected $fillable = array(
        "POST_ID", "CONTENT"
    );
}
