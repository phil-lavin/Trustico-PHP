<?php

spl_autoload_register(function($class) {
	$filename = __DIR__.'/'.str_replace('\\', '/', $class).'.php';

	if (!file_exists($filename)) {
		throw new \RuntimeException("Class {$class} not found ({$filename})");
	}

	require_once $filename;
});