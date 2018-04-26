<?php

namespace System\Components\Relationships;

class HasOne extends Relationship
{
	/**
     * Fetch the related object
     *
     * @return Model       The related model
     */
    public function fetch()
    {
        $foreignKey = $this->getKey(Relationship::KEY_FOREIGN);

        return $this->class::where($foreignKey, $this->sourceModel->getPrimaryKey())->get();
    }
}
