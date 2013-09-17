<?php

namespace Trustico\Response;

class Factory {
	public static function create($type, $class, $data) {
		$class_name = "\\Trustico\\Response\\{$type}\\{$class}";

		if (!class_exists($class_name)) {
			throw new \InvalidArgumentException("Class {$class_name} does not exist");
		}

		return $class_name::forge($data);
	}
}