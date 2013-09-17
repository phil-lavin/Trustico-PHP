<?php

namespace Trustico\Response\XML;

class ApproverList extends General {
	protected function __construct($xml) {
		parent::__construct($xml);

		if (!$this->_is_valid()) {
			throw new \OutOfBoundsException("XML supplied is not parsable as a Trustico ApproverList response");
		}
	}

	protected function _is_valid() {
		return (isset($this->sxml->Data->DomainEmailsReturned) && isset($this->sxml->Data->GenericEmailsReturned));
	}

	public function get_emails() {
		$domain_emails = $this->_get_by_prefix('DomainEmail', $this->sxml->Data->DomainEmailsReturned);
		$generic_emails = $this->_get_by_prefix('GenericEmail', $this->sxml->Data->GenericEmailsReturned);

		$emails = array_unique(array_merge($domain_emails, $generic_emails));

		foreach ($emails as $email) {
			yield $email;
		}
	}

	protected function _get_by_prefix($prefix, $count) {
		$data = [];

		for ($i = 1; $i <= $count; $i++) {
			$key = "{$prefix}_{$i}";

			if (isset($this->sxml->Data->$key)) {
				$data[] = (string)$this->sxml->Data->$key;
			}
		}

		return $data;
	}
}