<?php

namespace Trustico\Response\XML;

class General extends \Trustico\Response\AbstractBase {
	protected $sxml;

	protected function __construct($xml) {
		parent::__construct($xml);

		$this->sxml = @simplexml_load_string($xml);

		if (!$this->sxml || !$this->has_success_code()) {
			throw new \OutOfBoundsException("XML supplied is not parsable as a Trustico response");
		}
	}

	public function has_success_code() {
		return (isset($this->sxml->Data->SuccessCode));
	}

	public function is_success() {
		return (bool)(int)$this->sxml->Data->SuccessCode;
	}

	public function get_errors() {
		$errors = [];

		foreach ($this->sxml->Errors->Error as $error) {
			list($code, $message) = explode(':', $error);

			$errors[] = [
				'code' => $code,
				'message' => $message,
			];
		}

		return $errors;
	}

	public function get_data() {
		foreach ($this->sxml->Data->children() as $key=>$datum) {
			yield $key=>(string)$datum;
		}
	}

	public function __get($data_key) {
		if ( ! isset($this->sxml->Data->$data_key)) {
			throw new \OutOfBoundsException("No data for key {$data_key}");
		}

		return (string)$this->sxml->Data->$data_key;
	}
}