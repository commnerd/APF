<?php 

namespace System\Components;

use System\Interfaces\Request as RequestInterface;

/**
 * Request object used to gather information about the request
 */
class Request implements RequestInterface
{
	/**
	 * The request method (i.e. GET, POST, PUT, DELETE, HEAD, etc.)
	 * 
	 * @var string
	 */
	private $_method;

	/**
	 * Array of request header
	 * 
	 * @var array
	 */
	private $_headers;

	/**
	 * Arguments passed with request
	 *
	 * @var array
	 */
	private $_arguments;

	/**
	 * Constructor for a request
	 */
	public function __construct()
	{
		$this->_verb = isset($_REQUEST['_method']$_SERVER['REQUEST_METHOD'];

		$this->_headers = getallheaders();

		$this->_arguments = $_REQUEST;
	}

	/**
	 * Get a parameter passed in from the request
	 * 
	 * @param  string $name  The label for the corresponding argument
	 * @return string|array  The corresponding data
	 */
	public function __get($name) {
		return $_arguments[$name];
	}

	/**
	 * Retrieve header information
	 * 
	 * @param  string $name The name of the header to retrieve
	 * @return string       The header value
	 */
	public function getHeader($name) {
		return $_headers[$name];
	}

	/**
	 * Get the request method
	 * 
	 * @return string The request method
	 */
	public function getMethod()
	{
		return $_method;
	}
}

?>