<?php

namespace System\Components\Relationships;

class BelongsTo extends Belongs
{
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

        if(!isset($column)) {
            $this->column = $sourceModel->getPrimaryKey();
        }
        if(!isset($table)) {
            $this->column = $sourceModel->getTable();
        }
    }

	/**
     * Fetch the related object
     *
     * @return Model       The related model
     */
    public function fetch()
    {
        $key = $this->getKey(Relationship::KEY_LOCAL);

        return $this->class::where($key, $this->sourceModel->getKey())->get();
    }
}
