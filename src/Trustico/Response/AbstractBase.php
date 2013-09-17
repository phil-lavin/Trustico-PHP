<?php

namespace Trustico\Response;

abstract class AbstractBase {
	protected $data;

	protected function __construct($data) {
		$this->data = $data;
	}

	public static function forge($data) {
		return new static($data);
	}

	public abstract function has_success_code();

	public abstract function is_success();

	public abstract function get_errors();

	public function get_errors_string() {
		$out = '';

		foreach ($this->get_errors() as $error) {
			$out .= "{$error['message']}\n";
		}

		return trim($out);
	}

	public abstract function get_data();

	public abstract function __get($data_key);
}