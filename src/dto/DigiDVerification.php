<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;
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
