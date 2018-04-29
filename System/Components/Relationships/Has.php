<?php

namespace System\Components\Relationships;

use System\Services\TextTransforms;
use System\Components\Model;
use ReflectionClass;

/**
 * Convenience class for categorization
 */
abstract class Has extends Relationship
{
    /**
     * The string representing the context
     *
     * @var string
     */
    const CONTEXT_KEY = "foreign";

    /**
     * Build the relationship object
     *
     * @param Model  $sourceModel The model requesting the relationship
     * @param string $class       The target class
     * @param string $column      The overriding column
     * @param string $table       The overriding table
     */
    public function __construct(Model $sourceModel, $class, $column = null, $table = null)
    {
        parent::__construct($sourceModel, $class, $column, $table);

        $obj = new $class();
        if(!isset($column)) {
            $this->column = $obj->getPrimaryKey();
        }
        if(!isset($table)) {
            $this->table = $obj->getTable();
        }
    }

    /**
     * Get the key for the corresponding relationship
     * @param  string $context self::KEY_FOREIGN or self::KEY_LOCAL
     * @return string          The corresponding relationship key
     */
    protected function getKey($context)
    {
        if(isset($this->column)) {
            return $this->column;
        }

        $column = "";
        $class = $this->class;
        $obj = new $class();
        $reflection = new ReflectionClass($obj);
        $column = TextTransforms::camelCaseToSnakeCase($reflection->getShortName());
        $column .= "_".$this->sourceModel->getPrimaryKey();

        return strtoupper($column);
    }
}
