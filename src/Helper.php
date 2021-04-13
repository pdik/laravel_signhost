<?php

class Helper
{
    const API_VERSION = "v1";
	const CLIENT_VERSION = "2.0-beta-2";

	/** @var string */
	public $AppKey;

	/** @var string */
	public $ApiKey;

	/** @var string */
	public $SharedSecret;

	/** @var string */
	public $ApiEndpoint;

	/**
	 * @param string $appKey
	 * @param string $apiKey
	 * @param string $sharedSecret
	 * @param string $apiEndpoint
	 */
	function __construct()
	 {
		$this->AppKey       = config('signhost_config.app_key');
		$this->ApiKey       = config('signhost_config.user_token');
		$this->SharedSecret = config('signhost_config.secret_key');
		$this->ApiEndpoint  = config('signhost_config.api_endpoint');
	}
}