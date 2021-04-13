<?php
namespace pdik\signhost\DTO;

use pdik\signhost\DTO\verification;
use JsonSerializable;
class EidasLoginVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("eIDAS Login");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
