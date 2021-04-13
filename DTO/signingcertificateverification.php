<?php
namespace pdik\signhost\DTO;

use pdik\signhost\DTO\verification;
use JsonSerializable;


class SigningCertificateVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SigningCertificate");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
