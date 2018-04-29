<?php

namespace System\Components\Relationships;

use System\Services\TextTransforms;
use ReflectionClass;

/**
 * Convenience class for categorization
 */
abstract class Belongs extends Relationship
{
    /**
     * The string representing the context
     *
     * @var string
     */
    const CONTEXT_KEY = "local";

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
        $reflection = new ReflectionClass($this->sourceModel);
        $column = TextTransforms::camelCaseToSnakeCase($reflection->getShortName());
        $column .= "_".$obj->getPrimaryKey();

        return strtoupper($column);
    }

}
