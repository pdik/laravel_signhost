<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;
use JsonSerializable;
class ConsentVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("Consent");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
