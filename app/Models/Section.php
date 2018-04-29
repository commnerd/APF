<?php

namespace App\Models;

class Section extends Model
{
    protected $fillable = array(
        'LABEL'
    );

    public function ENTRIES()
    {
    	return $this->hasMany('\App\Models\Entry');
    }
}
