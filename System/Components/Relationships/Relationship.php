<?php

namespace System\Components\Relationships;

use System\Interfaces\Relationship as RelationshipInterface;

abstract class Relationship implements RelationshipInterface
{
    protected $class;

    protected $table;

    protected $column;

    public function __construct($class, $table = null, $column = null) {
        $this->class = $class;

        $this->table = $table;

        $this->column = $column;
    }
}
