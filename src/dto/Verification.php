<?php
namespace pdik\signhost\dto;

abstract class Verification {
	/** @var string */
	public $Type;

	/**
	 * @param string $type
	 */
	function __construct($type) {
		$this->Type = $type;
	}
}
