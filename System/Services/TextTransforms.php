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
}