<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;
use JsonSerializable;
class ItsmeIdentificationVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $PhoneNumber;

	/**
	 * @param string $phoneNumber
	 */
	function __construct($phoneNumber) {
		parent::__construct("itsme Identification");
		$this->PhoneNumber = $phoneNumber;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"        => $this->Type,
			"PhoneNumber" => $this->PhoneNumber,
		));
	}
}
