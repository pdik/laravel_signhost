<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;
use JsonSerializable;

class IDinVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("iDIN");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
