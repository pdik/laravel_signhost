<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;
use JsonSerializable;

class ItsmeSignVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("itsme sign");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
