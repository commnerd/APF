<?php

namespace System\Services;

/**
 * Toolkit for various text transformations
 */
class TextTransforms
{
	/**
	 * Change things like someAwesomeString to some_awsome_string
	 * @param  string $str The string to transform
	 * @return string      Snake case string representation
	 */
	public static function camelCaseToSnakeCase($str) {
		return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
	}

	/**
	 * Change things like some_awsome_string to someAwesomeString
	 * @param  string $str The string to transform
	 * @return string      Camel case string representation
	 */
	public static function snakeCaseToCamelCase($str) {
		return str_replace('_', '', ucwords($str, '_'));
	}
}
