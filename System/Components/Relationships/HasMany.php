<?php

namespace System\Components\Relationships;

class HasMany extends Relationship
{
    /**
     * Fetch the related object
     *
     * @param  Model $base The model to for the associated relationship
     * @return Model       The related model
     */
    public function fetch($base)
    {
        $obj = $this->class::where();

        if(empty($this->table)) {
            $this->table = $obj->getTable();
        }

        if(empty($this->column)) {
            $this->column = $obj->getPrimaryKey();
        }

        return $obj;
    }
}
