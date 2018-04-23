<?php

namespace System\Components;

use System\Services\TextTransforms;
use System\Components\DbConnection;


/**
 * Model for use by the DbConnection system
 */
class DbQuery extends PackageComponent
{
    protected $_query;

    protected $_bindings = null;

    public function __construct($query, $bindings)
    {
        $this->_query = $query;
        if(!empty($bindings[0])) {
            $this->_bindings = $bindings;
        }
    }
}
