<?php

namespace System\Components;

use \System\Components\Response;
use ErrorException;

class Response extends PackageComponent
{
	/**
	 * Response type constants
	 */
	const TYPE_TEMPLATE       = "template";
	const TYPE_REDIRECT       = "redirect";

	/**
	 * Error constant(s)
	 */
	const ERROR_TYPE_REDIRECT = "The redirect must have an associated route.";

	/**
	 * The response code to return to the browser
	 *
	 * @var integer
	 */
	protected $_code = 200;

	/**
	 * The type of response
	 *
	 * @var string
	 */
	protected $_type = "template";

	/**
	 * The template to use for the response if $_type === "template"
	 *
	 * @var string
	 */
	protected $_template;

	/**
	 * Parameters to pass to the template
	 *
	 * @var array
	 */
	protected $_params;

	/**
	 * Build the Response
	 *
	 * @param array $params The parameters to pass to the template
	 */
	public function __construct(array $params = array())
	{
		foreach(get_object_vars($this) as $key => $val) {
			$paramKey = substr($key, 1);
			if(isset($params[$paramKey])) {
				$this->{$key} = $params[$paramKey];
				unset($params[$paramKey]);
			}
		}

		$this->_params = $params;

		if($this->_type === self::TYPE_REDIRECT && !isset($params['route'])) {
			throw new ErrorException(self::ERROR_TYPE_REDIRECT);
		}
	}
}

?>
