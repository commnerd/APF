<?php

namespace App\Models;

use System\Components\Relationships\HasMany;

class Entry extends Model
{
    protected $fillable = array(
        'LABEL', 'CONTENT'
    );
}
