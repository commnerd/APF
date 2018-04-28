<?php

namespace System\Components;

use System\Services\TextTransforms;
use System\Components\DbConnection;
use System\Components\QueryBuilder;
use System\Interfaces\Relationship;
use ReflectionClass;

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
	 * Build queries to pass to the database handler
	 *
	 * @var QueryBuilder
	 */
	private $_queryBuilder;

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

	public static function all()
	{
		GLOBAL $app;

		$return = array();

		$class = get_called_class();

		$obj = new $class();

		$queryBuilder = new QueryBuilder($obj->getTable(), $obj->getPrimaryKeyColumn());

		$query = $queryBuilder->select();

		$results = $app->database->getCustomQueries($query);

		foreach($results as $row) {
			$obj = new $class();
			$return[] = $obj->fill($row)->toArray();
		}

		return $return;
	}

	/**
	 * Static method that leverages QueryBuilder
	 * @param  string $method      The name of the QueryBuilder method to call
	 * @param  array  $args        Arguments to pass to the method
	 * @return array|QueryBuilder  Result set or QueryBuilder
	 */
	public static function __callStatic($method, $args)
	{
		$class = get_called_class();

		$obj = new $class();

		$queryBuilder = new QueryBuilder($obj->getTable(), $obj->getPrimaryKeyColumn());

		$query = $queryBuilder->{$method}();

		$result = call_user_func_array(array($obj, $method), $args);

		return $result;
	}

	/**
	 * Forward function call if not explicitely defined
	 * @param  string $method The method to forward
	 * @param  array  $args   The args to pass
	 * @return mixed          The returned value
	 */
	public function __call($method, $args) {
		return call_user_func_array(array($this, "___".$method), $args);
	}

	/**
	 * Constructor for the class
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_queryBuilder = new QueryBuilder($this->getTable(), $this->getPrimaryKeyColumn());
		$this->_db = $this->app->database;

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
		$methods = get_class_methods(get_class($this));
		if(in_array($name, $methods)) {
			$relationship = $this->{$name}();
			if($relationship instanceof Relationship) {
				return $relationship->fetch();
			}
		}
		if($name === "attributes" && !isset($this->_attributes['attributes'])) {
			return $this->_attributes;
		}
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
	 * Grab the related model
	 * @param  string  $class      The related class
	 * @param  string  $foreignKey The foreign key to use in lookup
	 * @param  string  $table      The table to look in if needing override
	 * @return Model			   The associated model
	 */
	public function hasOne($class, $foreignKey = null, $table = null)
	{
		$relationship = new HasOne($this, $class, $foreignKey, $table);
		return $relationship->fetch();
	}

	/**
	 * Grab the related models
	 * @param  string  $class      The related class
	 * @param  string  $foreignKey The foreign key to use in lookup
	 * @param  string  $table      The table to look in if needing override
	 * @return Model			   The associated model
	 */
	public function hasMany($class, $foreignKey = null, $table = null)
	{
		$relationship = new HasMany($this, $class, $foreignKey, $table);
		return $relationship->fetch();
	}

	public function belongsTo($class, $foreignKey = null, $table = null)
	{
		$relationship = new BelongsTo($this, $class, $foreignKey, $table);
		return $relationship->fetch();
	}

	public function belongsToMany($class, $foreignKey = null, $table = null)
	{
		$relationship = new BelongsToMany($this, $class, $foreignKey, $table);
		return $relationship->fetch();
	}

	/**
	 * Get the table for the model
	 *
	 * @return string The table name for the model
	 */
	public function getTable()
	{
		if(isset($this->_table)) {
			return $this->_table;
		}
		$reflect = new ReflectionClass($this);
		return TextTransforms::camelCaseToSnakeCase($reflect->getShortName());
	}

	/**
	 * Get the primary key for the model
	 *
	 * @return string The column representing the primary key
	 */
	public function getPrimaryKeyColumn()
	{
		return $this->primaryKey;
	}

	/**
	 * Get the primary key value for the model
	 *
	 * @return integer The ID for the given model
	 */
	public function getPrimaryKey()
	{
		if(!isset($this->_attributes[$this->primaryKey])) {
			return null;
		}
		return $this->_attributes[$this->primaryKey];
	}

	/**
	 * Fill model from array
	 *
	 * @param  array  $attributes Array of items to populate model with
	 * @param  Model  $obj        An instance of the object making the call to
	 * 							  ensure only this object can fill liberally
	 * @return Model              Whatever was just filled
	 */
	public function fill($attributes) {
		if($this->_calledFromSystem()) {
			$this->_attributes = $attributes;
			$this->_originalValues = $attributes;
		}
		else {
			foreach($this->fillable as $key) {
				$this->_attributes[$key] = $attributes[$key];
			}
		}
		return $this;
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
		foreach($this->_attributes as $attribute => $value) {
			if($value instanceof Model) {
				$value->save($cascade);
			}
		}
		return $this->_attributes[$this->primaryKey];
	}

	/**
	 * Delete a given record
	 *
	 * @param boolean $cascade  If true, delete children and all subchildren
	 * @return void
	 */
	public function ___delete($id)
	{
		if(empty($this->primaryKey)) {
			throw new \ErrorException(self::ERROR_EXCEPTION_DELETE);
		}

		$column = $this->getPrimaryKeyColumn();
		$query = $this->_queryBuilder->where($column, $id)->delete();
		$this->_db->deleteRecord($query->query, $query->bindings);
	}

	/**
	 * Return all values as array
	 *
	 * @return array All attributes
	 */
	public function toArray()
	{
		return $this->_attributes;
	}

	public function readFromDatabase(DbQuery $query)
	{
		return $this->_db->getCustomQueries($query);
	}

	/**
	 * Update model in the database
	 *
	 * @return void
	 */
	private function _update() {
		$query = $this->_queryBuilder->update($this->toArray());
		$this->_db->updateRecord($query);
	}

	/**
	 * Insert model into the database
	 *
	 * @return integer Primary key
	 */
	private function _insert() {
		$query = $this->_queryBuilder->insert($this->toArray());
		$this->_db->addRecord($query);
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

	private function _runQuery($queryAndBindings)
	{
		if(is_array($queryAndBindings)) {
			$query = $queryAndBindings[0];
			$bindings = null;
			if(sizeof($queryAndBindings) === 2) {
				$bindings = $queryAndBindings[1];
			}
			return $app->database->getCustomQuery($query, $bindings);
		}
	}

	private function _calledFromSystem()
	{
		$trace = debug_backtrace();
		return preg_match('/^System/', $trace[2]['class']);
	}
}
