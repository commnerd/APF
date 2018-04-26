<?php

namespace System\Components;

class QueryBuilder extends AppComponent
{

    private $_table;

    private $_primaryKey;

    private $_orderBy;

    private $_columns;

    private $_values;

    private $_joins;

    private $_where;

    /**
     * The QueryBuilder constructor
     *
     * @param string $table      The table to construct the query against
     * @param string $primaryKey The primary key to construct the query against
     */
    public function __construct($table, $primaryKey)
    {
        $this->_table = $table;

        $this->_primaryKey = $primaryKey;

        $this->_where = array();

        $this->_orderBy = "";

        $this->_columns = array();

        $this->_joins = array();
    }

    /**
     * Create the given object
     *
     * @param  array $objArray Array to use to create the object
     * @return void
     */
    public function create($objArray)
    {
        $obj = new $this->_class();

        $obj->fill($objArray);

        $obj->save();

        return $obj;
    }

    public function get()
    {
        $obj = new $this->_class();

        list($query, $bindings) = $this->_buildSelectComponents();

        $ary = $this->database->getCustomQuery($query, $bindings);

        $obj->fill($ary);

        return $obj;
    }

    public function find($id)
    {
        $this->_where[$this->_obj->getPrimaryKeyColumn()] = $id;

        return $this->_buildSelectComponents();
    }

    public function with($componentTrail)
    {
        $this->_joins = explode('.', $componentTrail);

        return $this;
    }

    public function where($column, $op, $value = null)
    {
        if(empty($value)) {
            $this->_where[] = array('AND', $column, $op);
            return $this;
        }
        $this->_where[] = array('AND', $column, $op, $value);
        return $this;
    }

    public function orWhere($column, $op, $value = null)
    {
        if(empty($value)) {
            $this->_where[] = array('OR', $column, $op);
            return;
        }
        $this->_whereNot[] = array('OR', $column, $op, $value);

        return $this;
    }

    /**
     * Build insert components
     *
     * @param string The Key/Value pairs to be inserted
     * @return array Insert components
     */
    public function insert($columns)
    {
        $this->_columns = $columns;

        return $this->_buildInsertComponents();
    }

    /**
     * Build select components
     *
     * @param string The columns to select
     * @return DbQuery Select components
     */
    public function select($columns = null)
    {
        $this->_columns = $columns;

        return $this->_buildSelectComponents();
    }

    /**
     * Build update components
     *
     * @param string The Key/Value pairs to be updated
     * @return DbQuery Update components
     */
    public function update($columns)
    {
        $this->_columns = $columns;

        return $this->_buildSelectComponents();
    }

    /**
     * Build delete components
     *
     * @return DbQuery Delete components
     */
    public function delete()
    {
        return $this->_buildDeleteComponents();
    }

    /**
     * Automagically build fetch query and associated value map
     *
     * @return DbQuery Query and value map
     */
    private function _buildSelectComponents()
    {
        $qry = "SELECT COLS FROM `".$this->_table."`";
        $qryMap = "";
        $keys = array();
        $values = array();
        if(empty($this->_columns)) {
            $qry = preg_replace('/COLS/', '*', $qry);
        }
        else {
            $qry = preg_replace(
                '/COLS/',
                '`'.implode('`,`', $this->_columns).'`',
                $qry
            );
        }

        if(!empty($this->_where)) {
            $qry .= " WHERE ";
        }

        foreach($this->_where as $key => $value) {
            $input = $value[2];
            if(sizeof($value) == 4) {
                $input = $value[3];
            }
            if($key > 0) {
                $qry .= $value[0]." ";
            }
            $qry .= "`".$value[1]."` = ?";
            $qryMap .= $this->_getQryMapValueType($input);
            $values[] = $input;
        }

        if(!empty($this->_orderBy)) {
            $qry .= " ORDER BY `$this->_orderBy`";
        }

        return new DbQuery($qry, array_merge(array($qryMap), $values));
    }

    /**
	 * Automagically build the insert query and associated value map
	 *
	 * @return DbQuery  Query and value map
	 */
	private function _buildInsertComponents() {
		$qry = "INSERT INTO `".$this->_table."` (`KEYS`) VALUES (VALS)";
		$qryMap = "";
		$keys = array();
		$values = array();
		$questionMarks = array();
		foreach($this->_columns as $key => $value) {
			$qryMap .= $this->_getQryMapValueType($value);
			array_push($keys, $key);
			array_push($values, $value);
			array_push($questionMarks, '?');
		}
		$qry = preg_replace('/KEYS/', implode('`,`', $keys), $qry);
		$qry = preg_replace('/VALS/', implode(",", $questionMarks), $qry);

		return new DbQuery($qry, array_merge(array($qryMap), $values));
	}

	/**
	 * Automagically build the update query and associated value map
	 *
	 * @return DbQuery  Query and value map
	 */
	private function _buildUpdateComponents() {
		$qry = "UPDATE `".$this->_table."` SET ";
        $qryMap = "";

        $updates = $this->_obj->toArray();
        unset($updates[$this->_primaryKey]);

        list($qryUpdate, $qryMapUpdate) = $this->_inputBuilder("updates");
        $qry .= $qryUpdate;
        $qryMap .= $qryMapUpdate;

        $qry .= " WHERE ";

        list($qryUpdate, $qryMapUpdate) = $this->_inputBuilder("where");
		$qry .= $qryUpdate;
        $qryMap .= $qryMapUpdate;

        $qry .= "`".$this->_primaryKey."` = ?";

        $updates = "`$key` = ?";
        $qryMap .= "i";

		return new DbQuery($qry, array_merge(array($qryMap), $values));
	}

    /**
     * A generic `key` = 'val' pair builder
     *
     * @param  string $section The variable to pull values from
     * @return array           Array (query extension, query map extension)
     */
    private function _inputBuilder($section) {
        $qryMap = "";
        $array = ($section === "updates") ? $this->_columns : $this->{"_".$section};
        foreach($array as $key => $map) {
            $value = $this->_getValue($section, $value);
            if($key !== $this->_primaryKey) {
                $qryMap .= $this->_getQryMapValueType($value);
                $subQry .= "`$key` = ?";
            }
        }
        return array($subQry, $qryMap);
    }

	/**
	 * Map value to query map character
	 *
	 * @param  mixed  $value The value to be mapped
	 * @return char          The character representing the DB type
	 */
	private function _getQryMapValueType($value) {
		if(is_integer($value)) {
			return "i";
		}
		return "s";
	}

    /**
     * Pull value from various context maps
     * @param  string $section Variable context (where, updates, etc.)
     * @param  array  $map     Information used to build subquery (glue, qryMapValueType, value, qryString)
     * @return string          The value pulled from the map
     */
    private function _getMetaFromMap($section, $map)
    {
        switch($section) {
            case 'updates':
                $value = $map[sizeof($map) - 1];
                return array(',', $this->_getQryMapValueType($value), $value, "`".$map[0]."` = ?");
            case 'where':
                $value = $map[sizeof($map) - 1];
                $modifier = $map[0];
                return $map[sizeof($map) - 1];
        }
    }
}
