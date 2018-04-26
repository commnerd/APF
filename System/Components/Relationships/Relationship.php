<?php

namespace System\Components\Relationships;

use System\Interfaces\Relationship as RelationshipInterface;
use System\Services\TextTransforms;

abstract class Relationship implements RelationshipInterface
{
    const KEY_FOREIGN = "foreign";
    const KEY_LOCAL   = "local";

	protected $sourceModel;

    protected $class;

    protected $table;

    protected $column;

    public function __construct($sourceModel, $class, $column = null, $table = null) {
    	$this->sourceModel = $sourceModel;

        $this->class = $class;

        $this->table = $table;

        $this->column = $column;
    }

    protected function getKey($context)
    {
        if(isset($this->column)) {
            return $this->column;
        }

        $reflect = null;
        switch($context) {
            case self::KEY_FOREIGN:
                $reflect = new ReflectionClass(new $this->class());
                break;
            case self::KEY_LOCAL
                $reflect = new ReflectionClass($this->sourceModel);
                break;
        }
        
        $name = $reflect->getShortName();
        return strtoupper(TextTransforms::camelCaseToSnakeCase($name));
    }
}
