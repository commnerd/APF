<?php

namespace App\Models;

use System\Components\Relationships\HasMany;

class Section extends Model
{
    protected $fillable = array(
        'LABEL',
    );

    public function SECTIONS()
    {
    	return $this->hasMany('\App\Models\Entry');
    }
}
