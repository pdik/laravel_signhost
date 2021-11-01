<?php
namespace  pdik\signhost\dto;
// Required due to inheritance.
require_once("Verification.php");

class SurfnetVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SURFnet");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
