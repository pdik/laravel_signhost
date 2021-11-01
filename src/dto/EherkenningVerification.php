<?php
namespace pdik\signhost\dto;

use pdik\signhost\dto\verification;

class EherkenningVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $EntityConcernIdKvkNr;

	/**
	 * @param string $entityConcernIdKvkNr
	 */
	function __construct($entityConcernIdKvkNr = null) {
		parent::__construct("eHerkenning");
		$this->EntityConcernIdKvkNr = $entityConcernIdKvkNr;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"                 => $this->Type,
			"EntityConcernIdKvkNr" => $this->EntityConcernIdKvkNr,
		));
	}
}
