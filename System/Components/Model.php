<?php

namespace System\Components;

use System\Services\TextTransforms;
use System\Components\DbConnection;

/**
 * Model for use by the system
 */
abstract class Model extends AppComponent
{
	/**
	 * Deletion error exception message
	 *
	 * @var string
	 */
	const ERROR_EXCEPTION_DELETE = "No context for deletion.";

	/**
	 * Deletion error exception message
	 *
	 * @var string
	 */
	const ERROR_EXCEPTION_UPDATE = "No context for update.";

	/**
	 * The database connection to work with
	 *
	 * @var DbConnection
	 */
	private $_db;

	/**
	 * Attributes to be handled by this class
	 *
	 * @var array
	 */
	private $_attributes;

	/**
	 * Values as read from database
	 */
	private $_originalValues;

	/**
	 * Table maintaining this class's data
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Primary key for the class
	 *
	 * @var string
	 */
	protected $primaryKey = "ID";

	/**
	 * Casting declarations to maintain for this class
	 *
	 * @var array
	 */
	protected $casts;

	/**
	 * The array of variables intended for automagic filling
	 *
	 * @var array
	 */
	protected $fillable;

	/**
	 * Constructor for the class
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_db = $this->app->getDbConnection();
		$this->_attributes = array();

		if(empty($this->table) || !is_string($this->table)) {
			$className = $this->_getClassName();
			$this->table = TextTransforms::camelCaseToSnakeCase($className);
		}

		$this->_instantiateArrayIfNecessary($this->casts);
		$this->_instantiateArrayIfNecessary($this->fillable);
		$this->_instantiateArrayIfNecessary($this->_originalValues);
	}

	/**
	 * Automagic variable retrieval
	 *
	 * @param  string $name Name of variable to retrieve
	 * @return mixed        Value of retrieved variable
	 */
	public function __get($name)
	{
		return $this->_attributes[$name];
	}

	/**
	 * Automagic variable setting
	 *
	 * @param string $name  Name of variable to retrieve
	 * @param mixed  $value Value of variable
	 */
	public function __set($name, $value)
	{
		$this->_attributes[$name] = $value;
	}

	/**
	 * Fill model from array
	 *
	 * @param  array  $attributes Array of items to populate model with
	 * @return void
	 */
	public function fill($attributes) {
		foreach($this->fillable as $key) {
			$this->_attributes[$key] = $attributes[$key];
		}
	}

	/**
	 * Add or update the model in the database
	 *
	 * @param boolean $cascade  If true, delete children and all subchildren
	 * @return integer          The primary key value of the saved item
	 */
	public function save($cascade = false)
	{
		if(isset($this->_attributes[$this->primaryKey])) {
			$this->_update();
		}
		else {
			$this->_attributes[$this->primaryKey] = $this->_insert();
		}
		foreach($this->attributes as $attribute => $value) {
			if($value instanceof Model) {
				$value->save($cascade);
			}
		}
		return $this->_attributes[$this->primaryKey];
	}

	/**
	 * Update model in the database

	 * @return void
	 */
	private function _update() {
		list($qry, $qryMap) = $this->_buildUpdateComponents();

	}

	/**
	 * Insert model into the database
	 *
	 * @return integer Primary key
	 */
	private function _insert() {
		list($qry, $qryMap) = $this->_buildInsertComponents();
	}

	/**
	 * Delete a given record
	 *
	 * @param boolean $cascade  If true, delete children and all subchildren
	 * @return void
	 */
	public function delete($cascade = false)
	{
		if(empty($this->primaryKey)) {
			throw new \ErrorException(self::ERROR_EXCEPTION_DELETE);
		}
		foreach($this->attributes as $attribute => $value) {
			if($value instanceof Model) {
				$value->delete($cascade);
			}
		}
	}

	/**
	 * Get the class name sans namespace
	 *
	 * @return string Class name
	 */
	private function _getClassName()
	{
		return (new \ReflectionClass($this))->getShortName();
	}

	/**
	 * Utility function to make an array if it's supposed to be an array and it's not
	 *
	 * @param  mixed  &$var The variable to transform
	 * @return void
	 */
	private function _instantiateArrayIfNecessary(&$var) {
		if(empty($var) || !is_array($var)) {
			$var = array();
		}
	}

	/**
	 * Automagically build the insert query and associated value map
	 *
	 * @return array  Query and value map
	 */
	private function _buildInsertComponents() {
		$qry = "INSERT INTO `$this->table` ('KEYS') VALUES ('VALS')";
		$qryMap = "";
		$keys = array();
		$values = array();
		$questionMarks = array();
		foreach($this->_attributes as $key => $value) {
			$qryMap .= $this->_getQryMapValueType($value);
			array_push($keys, $key);
			array_push($values, $value);
			array_push($questionMarks, '?');
		}
		$qry = preg_replace('/KEYS/', implode('`,`', $keys), $qry);
		$qry = preg_replace('/VALS/', implode(",", $questionMarks), $qry);

		return array($qry, array_merge(array($qryMap), $values));
	}

	/**
	 * Automagically build the update query and associated value map
	 *
	 * @return array  Query and value map
	 */
	private function _buildUpdateComponents() {
		$qry = "UPDATE `$this->table` SET UPDATES WHERE `$this->primaryKey` = ?";
		$qryMap = "";
		$updates = "";
		foreach($this->_attributes as $key => $value) {
			if($key !== $this->primaryKey) {
				$qryMap .= $this->_getQryMapValueType($value);
				$updates = "`$key` = ?";
			}
		}
		$qryMap .= "i";
		$values[] = $this->_attributes[$this->primaryKey];

		$qry = preg_replace('/VALS/', implode("', '", $updates), $qry);

		return array($qry, array_merge(array($qryMap), $values));
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
}
