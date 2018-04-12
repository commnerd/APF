<?php

namespace System\Components;

/**
 * Service for scanning directories
 */
class DirectoryScanner
{
	const EXCEPTION_PATH_NOT_FOUND = "The path to the given directory does not exist.";

	const EXCEPTION_PATH_NOT_DIRECTORY = "The defined path is not a directory.";

	public static function checkPath($path)
	{
		if(!file_exists($path)) {
			throw \ErrorException(DirectoryScanner::EXCEPTION_PATH_NOT_FOUND);
		}
	}

	public static function checkDirectory($path)
	{
		if(!file_dir($path)) {
			throw \ErrorException(DirectoryScanner::EXCEPTION_PATH_NOT_DIRECTORY);
		}
	}

	public static function getFiles($path)
	{
		$contents = DirectoryScanner::getDirContents();
		$files = array();
		foreach($contents as $inode) {
			if(!is_dir($inode)) {
				array_push($files, $path.DIRECTORY_SEPARATOR.$inode);
			}
		}
		return $files;
	}

	public static function getDirs($path)
	{
		$contents = DirectoryScanner::getDirContents();
		$excludes = array('.', '..');
		$dirs = array();
		foreach($contents as $inode) {
			if(is_dir($inode) && !in_array($inode, $excludes)) {
				array_push($files, $path.DIRECTORY_SEPARATOR.$inode);
			}
		}

		return $dirs;
	}

	public static function getDirContents($path) {
		DirectoryScanner::checkPath($path);
		DirectoryScanner::checkDirectory($path);

		return scandir($path);
	}
}

?>