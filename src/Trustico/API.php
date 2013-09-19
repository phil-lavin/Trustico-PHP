<?php

namespace Trustico;

class API {
	const API_URL = "https://api.ssl-processing.com/geodirect/postapi/";

	protected $username;
	protected $password;
	protected $format;

	protected function __construct($username, $password, $format = 'XML') {
		$this->username = $username;
		$this->password = $password;
		$this->format = $format;
	}

	public static function forge($username, $password) {
		return new static($username, $password);
	}

	public function test() {
		$msg = "System Test";

		try {
			$hello = $this->hello($msg);

			return ($hello->TextToEcho === $msg);
		}
		catch (\Exception $e) {
			return false;
		}
	}

	public function hello($message) {
		return $this->_request('Hello', ['TextToEcho' => $message]);
	}

	public function order_status($order_id) {
		return $this->_request('GetStatus', ['OrderID' => $order_id]);
	}

	public function resend_approver_email($order_id) {
		return $this->_request('ResendEmail', ['OrderID' => $order_id, 'EmailType' => 'Approver']);
	}

	public function resend_fulfilment_email($order_id) {
		return $this->_request('ResendEmail', ['OrderID' => $order_id, 'EmailType' => 'Fulfillment']);
	}

	public function change_approver_email($order_id, $email) {
		return $this->_request('ChangeApproverEmail', ['OrderID' => $order_id, 'ApproverEmail' => $email]);
	}

	public function reissue($order_id, $email, $csr) {
		return $this->_request('Reissue', ['OrderID' => $order_id, 'Email' => $email, 'CSR' => $csr]);
	}

	public function get_user_agreement($product_name) {
		return $this->_request('GetUserAgreement', ['ProductName' => $product_name]);
	}

	public function get_approver_list($domain) {
		return $this->_request('GetApproverList', ['Domain' => $domain], 'ApproverList');
	}

	public function process_order_type_1($data) {
		$required = [
			'AdminTitle',
			'AdminFirstName',
			'AdminLastName',
			'AdminOrganization',
			'AdminRole',
			'AdminEmail',
			'AdminPhoneCC',
			'AdminPhoneAC',
			'AdminPhoneN',
			'AdminAddress1',
			'AdminCity',
			'AdminState',
			'AdminPostCode',
			'AdminCountry',
			'ProductName',
			'CSR',
			'ValidityPeriod',
			'Insurance',
			'ServerCount',
			'ApproverEmail',
		];

		if (!isset($data['TechUseReseller']) || !$data['TechUseReseller']) {
			$required += [
				'TechTitle',
				'TechFirstName',
				'TechLastName',
				'TechOrganization',
				'TechEmail',
				'TechPhoneCC',
				'TechPhoneAC',
				'TechPhoneN',
				'TechAddress1',
				'TechCity',
				'TechState',
				'TechPostCode',
				'TechCountry',
			];
		}

		$default = [
			'AgreedToTerms' => 1,
		];

		$data = array_merge($default, $data);


		if (($error_fields = $this->_validate_fields($required, $data)) !== true) {
			$fields = implode(', ', $error_fields);

			throw new \InvalidArgumentException("The following fields are required but not supplied: {$fields}");
		}

		return $this->_request('ProcessType1', $data);
	}

	public function process_order_type_2($data) {
		$required = [
			'OrgName',
			'OrgAddress1',
			'OrgCity',
			'OrgState',
			'OrgPostCode',
			'OrgCountry',
			'OrgPhoneCC',
			'OrgPhoneAC',
			'OrgPhoneN',
			'AdminTitle',
			'AdminFirstName',
			'AdminLastName',
			'AdminRole',
			'AdminEmail',
			'ProductName',
			'CSR',
			'ValidityPeriod',
			'Insurance',
			'ServerCount',
			'ApproverEmail',
		];

		if (!isset($data['TechUseReseller']) || !$data['TechUseReseller']) {
			$required += [
				'TechTitle',
				'TechFirstName',
				'TechLastName',
				'TechOrganization',
				'TechEmail',
				'TechPhoneCC',
				'TechPhoneAC',
				'TechPhoneN',
				'TechAddress1',
				'TechCity',
				'TechState',
				'TechPostCode',
				'TechCountry',
			];
		}

		$default = [
			'AgreedToTerms' => 1,
		];

		$data = array_merge($default, $data);


		if (($error_fields = $this->_validate_fields($required, $data)) !== true) {
			$fields = implode(', ', $error_fields);

			throw new \InvalidArgumentException("The following fields are required but not supplied: {$fields}");
		}

		return $this->_request('ProcessType2', $data);
	}

	protected function _request($command, array $data = [], $class = 'General') {
		$fields = array_merge(
			$data,
			[
				'Command' => $command,
				'ResponseType' => $this->format,
				'UserName' => $this->username,
				'Password' => $this->password,
			]
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, static::API_URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		$result = curl_exec($ch);

		if($result === false) {
			throw new TrusticoAPIException("Curl errored! Message: ".curl_error($ch));
		}

		curl_close($ch);

		return Response\Factory::create($this->format, $class, trim($result));
	}

	protected function _validate_fields($required, $data) {
		$diff = array_diff($required, array_keys($data));

		if (!$diff) {
			return true;
		}

		return $diff;
	}
}

class TrusticoAPIException extends \Exception {}