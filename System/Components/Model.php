<?php

namespace System\Components;

use System\Components\Relationships\BelongsToMany;
use System\Components\Relationships\BelongsTo;
use System\Components\Relationships\HasMany;
use System\Components\Relationships\HasOne;
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
	 * Fetch error when element not found
	 *
	 * @var string
	 */
	const ERROR_EXCEPTION_GET    = "Variable not found.";

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
	 * "With" registry
	 * @var [type]
	 */
	private $_with;

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
	 * Static method that leverages QueryBuilder
	 * @param  string $method      The name of the QueryBuilder method to call
	 * @param  array  $args        Arguments to pass to the method
	 * @return array|QueryBuilder  Result set or QueryBuilder
	 */
	public static function __callStatic($method, $args)
	{
		$class = get_called_class();

		$obj = new $class();
		if($obj instanceof Model) {
			return call_user_func_array(array($obj, $method), $args);
		}

		return $obj;
	}

	/**
	 * Forward function call if not explicitely defined
	 * @param  string $method The method to forward
	 * @param  array  $args   The args to pass
	 * @return mixed          The returned value
	 */
	public function __call($method, $args) {

		$methods = get_class_methods($this);
		if(in_array("___".$method, $methods)) {
			return call_user_func_array(array($this, "___".$method), $args);
		}

		$methods = get_class_methods($this->_queryBuilder);
		if(in_array($method, $methods)) {
			call_user_func_array(array($this->_queryBuilder, $method), $args);
			return $this;
		}

	}

	/**
	 * Constructor for the class
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_queryBuilder = new QueryBuilder($this->getTable(), $this->getPrimaryKey());
		$this->_db = $this->app->database;

		$this->_attributes = array();
		$this->_with = array();

		if(empty($this->table) || !is_string($this->table)) {
			$className = $this->_getClassName();
			$this->table = $this->getTable();
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
		if(isset($this->_attributes[$name])) {
			return $this->_attributes[$name];
		}
		if(in_array($name, $methods)) {
			$relationship = $this->{$name}();
			if($relationship instanceof Relationship) {
				$query = $relationship->getQuery();
				$results = $this->app->database->getCustomQueries($query);
				return $relationship->buildResultSet($results);
			}
		}
		if($name === "attributes" && !isset($this->_attributes['attributes'])) {
			return $this->_attributes;
		}

		throw new ErrorException(self::ERROR_EXCEPTION_GET);
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
		$name = $reflect->getShortName();
		$name = TextTransforms::camelCaseToSnakeCase($name);
		return TextTransforms::singleToPlural($name);
	}

	/**
	 * Get the primary key for the model
	 *
	 * @return string The column representing the primary key
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	/**
	 * Get the primary key value for the model
	 *
	 * @return integer The ID for the given model
	 */
	public function getKey()
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
	public function fill($attributes, $results = null) {
		if($this->_calledFromSystem()) {
			foreach(array_keys($attributes) as $attribute) {
				$table = $this->table;

				if(preg_match("/^".$table."_(.*)$/", $attribute, $matches)) {
					$this->_attributes[$matches[1]] = $attributes[$attribute];
					$this->_originalValues[$matches[1]] = $attributes[$attribute];
				}

			}
			if(!empty($this->_with)) {
				foreach($this->_with as $key => $relation) {
					$this->_attributes[$key] = $this->fillChildren($results, $relation);
				}
			}
		}
		else {
			foreach($this->fillable as $key) {
				$this->_attributes[$key] = $attributes[$key];
			}
		}
		return $this;
	}

	public function fillChildren(array $results, Relationship $relation = null)
	{
		if(!isset($relation)) {
			foreach($this->_with as $relation) {
				$this->fillChildren($results, $relation);
			}
			return;
		}
		$objs = array();
		$class = $relation->getClass();

		foreach($results as $row) {
			$obj = new $class();
			if(!empty($relation->getWith())) {
				$obj->with($relation->getWith());
			}

			$objs[$row[$obj->getTable()."_".$obj->getPrimaryKey()]] = $obj->fill($row, $results);
		}

		return $objs;
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

/*
	private function ___buildCascadingArraysFromModelArray(array $models, $childModels = array())
	{
		$results = array();
		foreach($models as $model) {
			$results[] = $this->___buildCascadingArraysFromModel($model, $childModels);
		}
		return $results;
	}

	private function ___buildCascadingArraysFromModel(Model $model, $childModels = array())
	{
		// Return early with model if we're at "leaf" model in tree
		if(empty($childModels)) {
			return $model->toArray();
		}

		$arrayedModel = $model->toArray();
		if(is_array($childModels)) {
			foreach($childModels as $childModel) {
				$this->___buildCascadingArraysFromModel($model, $childModel);
			}
		}
		if(!empty($childModels) && is_string($childModels)) {
			$childModels = explode('.', $childModels);
			$childModel = array_pop($childModels);
			$childModels = implode('.', $childModels);
			$arrayedModel[$childModel] = $this->___buildCascadingArraysFromModelArray($model->{$childModel}, $childModels);
		}
		return $arrayedModel;
	}
	*/

	private function ___with($children, QueryBuilder $qb = null)
	{
		if(empty($qb)) {
			$qb = $this->_queryBuilder;
		}
		if(is_array($children)) {
			foreach($children as $childrenStrings) {
				$this->with($childrenStrings, $qb);
			}
		}
		$children = explode('.', $children);
		$child = array_pop($children);
		$children = implode('.', $children);
		$relation = $this->{$child}();
		$qb->join($relation);
		if(!empty($children)) {
			$relation->setWith($children);
		}
		$this->_with[$child] = $relation;
		return $this;
	}


	private function ___all()
	{
		$query = call_user_func_array(array($this->_queryBuilder, 'get'), array());

		$results = $this->app->database->getCustomQueries($query);
		$objs = array();
		if(!empty($results)) {
			foreach($results as $row) {
				$objs[$row[$this->getTable()."_".$this->getPrimaryKey()]] = $this->fill($row, $results);
			}
		}
		return $objs;
	}

	/**
	 * Delete a given record
	 *
	 * @param boolean $cascade  If true, delete children and all subchildren
	 * @return void
	 */
	private function ___delete($id)
	{
		if(empty($this->primaryKey)) {
			throw new \ErrorException(self::ERROR_EXCEPTION_DELETE);
		}

		$column = $this->getPrimaryKey();
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
		foreach($this->_attributes as $key => $value) {
			if(is_array($value)) {
				foreach($value as $index => $item) {
					if($item instanceof Model) {
						$this->_attributes[$key][$index] = $item->toArray();
					}
				}
			}
			if($value instanceof Model) {
				$this->_attributes[$key] = $value->toArray();
			}
		}
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
