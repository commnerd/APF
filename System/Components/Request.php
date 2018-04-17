<?php

namespace System\Components;

use System\Interfaces\Request as RequestInterface;

/**
 * Request object used to gather information about the request
 */
class Request extends AppComponent implements RequestInterface
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
	 * The URL being requested
	 *
	 * @var string
	 */
	private $_requestUrl;

	/**
	 * Constructor for a request
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_method = isset($_REQUEST['_method']) ? $_REQUEST['_method'] : $_SERVER['REQUEST_METHOD'];

		$this->_headers = $_SERVER;

		$this->_arguments = $_REQUEST;

		$this->_requestUrl = $_SERVER['REQUEST_URI'];
	}

	/**
	 * Get a parameter passed in from the request
	 *
	 * @param  string $name  The label for the corresponding argument
	 * @return string|array  The corresponding data
	 */
	public function __get($name) {
		return $this->_arguments[$name];
	}

	/**
	 * Retrieve header information
	 *
	 * @param  string $name The name of the header to retrieve
	 * @return string       The header value
	 */
	public function getHeader($name) {
		return $this->_headers['HTTP_'.strtoupper($name)];
	}

	/**
	 * Get the request method
	 *
	 * @return string The request method
	 */
	public function getMethod()
	{
		return $this->_method;
	}

	/**
	 * Get the current URL
	 *
	 * @return string The requested URL
	 */
	public function getUrl()
	{
		return $this->_requestUrl;
	}
}

?>