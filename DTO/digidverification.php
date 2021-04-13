<?php
namespace pdik\signhost\DTO;

use pdik\signhost\DTO\verification;
use JsonSerializable;
class DigiDVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $Bsn;

	/**
	 * @param string $bsn
	 */
	function __construct($bsn = null) {
		parent::__construct("DigiD");
		$this->Bsn = $bsn;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
			"Bsn"  => $this->Bsn,
		));
	}
}
