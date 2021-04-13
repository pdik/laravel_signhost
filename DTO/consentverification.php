<?php
namespace pdik\signhost\DTO;

use pdik\signhost\DTO\verification;
// Required due to inheritance.

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
