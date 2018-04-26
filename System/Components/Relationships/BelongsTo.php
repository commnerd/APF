<?php

namespace System\Components\Relationships;

class BelongsTo extends Relationship
{
	/**
     * Fetch the related object
     *
     * @return Model       The related model
     */
    public function fetch()
    {
        $key = $this->getKey(Relationship::KEY_LOCAL);

        return $this->class::where($key, $this->sourceModel->getPrimaryKey())->get();
    }
}
