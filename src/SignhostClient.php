<?php

namespace pdik\signhost;

use pdik\signhost\Exception\SignhostException;
use InvalidArgumentException;

/**
 * @author Pepijn dik <pepijn@pdik.nl>
 */
class SignhostClient
{
    /**
     * @var array
     */

    const API_VERSION = "v1";
    const CLIENT_VERSION = "2.0-beta-2";

    const OPT_CAINFOPATH = 'ca-info-path';
    const OPT_URL = 'url';
    const OPT_TIMEOUT = 'timeout';
    const OPT_IGNORE_HTTP_ERRORS = 'ignore-http-errors';

    private static $KNOWN_OPTIONS = [
        self::OPT_CAINFOPATH,
        self::OPT_URL,
        self::OPT_TIMEOUT,
        self::OPT_IGNORE_HTTP_ERRORS
    ];
    /** @var string */
    public $AppKey;
    /** @var string */
    public $appName;
    /** @var string */
    public $ApiKey;

    /** @var string */
    public $SharedSecret;

    /** @var string */
    public $ApiEndpoint;


    private $headers;

    /**
     * @param  string  $appKey
     * @param  string  $apiKey
     * @param  string  $sharedSecret
     * @param  string  $apiEndpoint
     */
    function __construct(array $requestOptions = [])
    {
        $this->appName = config('signhost_config.app_name');
        $this->AppKey = config('signhost_config.app_key');
        $this->ApiKey = config('signhost_config.user_token');
        $this->SharedSecret = config('signhost_config.secret_key');
        $this->ApiEndpoint = config('signhost_config.api_endpoint');

        $this->headers = [
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Content-Type: application/json",
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ];
        $this->requestOptions = $requestOptions;
        foreach ($this->requestOptions as $optionName => $value) {
            if (!in_array($optionName, self::$KNOWN_OPTIONS, true)) {
                throw new InvalidArgumentException("Unknown request option: $optionName");
            }
        }
    }

    private function shouldIgnoreHttpErrors(): bool
    {
        return $this->requestOptions[self::OPT_IGNORE_HTTP_ERRORS] ?? false;
    }

    /**
     * @throws SignhostException
     */
    public function performRequest(string $endpoint, string $method, $data = null, $filePath = null)
    {
        $uploadFileHandle = null;

        try {
            $headers = $this->headers;
            $targetUrl = $this->requestOptions[self::OPT_URL] ?? $this->ApiEndpoint;

            // When $filepath is set, provide a file descriptor to curl so it can use it to send
            // the file along with the request.
            if (isset($filePath)) {
                $uploadFileHandle = fopen($filePath, 'rb');

                $headers[] = 'Content-Type: application/pdf';
                $headers[] = 'Digest: SHA256='.base64_encode(pack('H*', hash_file('sha256', $filePath)));
            }

            // When data is set, we must add Content-Type: application/json header
            if (isset($data) && !empty($data)) {
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: '.strlen(json_encode($data));
            }

            // Initialize a cURL session
            return $this->performCURLRequest(
                $method,
                $targetUrl.$endpoint,
                $headers,
                $data,
                $filePath,
                $uploadFileHandle
            );
        } finally {
            // when $fh is set for file upload we must close it for free up memory and remove any lock
            if (isset($uploadFileHandle)) {
                // close file handler
                fclose($uploadFileHandle);
            }
        }
    }

    private function performCURLRequest(
        string $method,
        string $url,
        array $headers = [],
        $data = null,
        $filePath = null,
        $fileHandle = null
    ) {

        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        switch ($method) {
            case 'DELETE':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;

            case 'PUT':
                if (!empty($data) && empty($filePath)) {
                    curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
                } elseif (empty($data) && !empty($filePath)) {
                    curl_setopt($curlHandle, CURLOPT_PUT, 1);
                    curl_setopt($curlHandle, CURLOPT_INFILESIZE, filesize($filePath));
                    curl_setopt($curlHandle, CURLOPT_INFILE, $fileHandle);
                } else {
                    curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, '');
                }
                break;

            case 'POST':
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
                break;
        }
        // Tell curl what timeout to use
        if (isset($this->requestOptions[self::OPT_TIMEOUT])) {
            curl_setopt($curlHandle, CURLOPT_TIMEOUT, $this->requestOptions[self::OPT_TIMEOUT]);
        }

        // Tell curl where to find the root CA's we trust.
        if (isset($this->requestOptions[self::OPT_CAINFOPATH])) {
            curl_setopt($curlHandle, CURLOPT_CAINFO, $this->requestOptions[self::OPT_CAINFOPATH]);
        }

        // We want the response returned from curl_exec()
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

        // Don't reuse connections
        curl_setopt($curlHandle, CURLOPT_FRESH_CONNECT, 1);

        // Make sure the x509 certificate presented is valid
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);

        $response = new Response($curlHandle);
        // Set the headers and perform the request.
        $this->assertSuccessfulResponse($curlHandle, $response);
        curl_close($curlHandle);
        return $response;
    }
     /**
     * Check http status code for errors
     * @throws SignHostException
     */
    private function assertSuccessfulResponse($curlHandle, $response)
    {
        if (curl_errno($curlHandle)) {
            throw new SignhostException(
                'Request to Signhost failed: ' . curl_error($curlHandle),
                0
            );
        }

        $statusCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        if ($statusCode < 400 || $this->shouldIgnoreHttpErrors()) {
            return;
        }

        $message = 'Unknown error';
        if ($statusCode >= 400 && $statusCode <= 499) {
            // decode message from json string
            $object = json_decode($response, false);
            $message = $object->Message ?? 'Unknown client error';
        } elseif ($statusCode >= 500 && $statusCode <= 599) {
            $object = json_decode($response, false);
            $message = $object->Message ?? 'Internal server error on remote server';
        }

        throw new SignhostException(
            "Response code: $statusCode, message: $message",
            $statusCode
        );
    }

    /**
     * Generates a checksum and validates it with the remote checksum.
     * @param  string  $masterTransactionId
     * @param  string  $fileId
     * @param  int  $status
     * @param  string  $remoteChecksum
     * @return bool
     */
    public function ValidateChecksum($masterTransactionId, $fileId, $status, $remoteChecksum)
    {
        $localChecksum = sha1($masterTransactionId."|".$fileId."|".$status."|".$this->SharedSecret);

        if (strlen($localChecksum) !== strlen($remoteChecksum)) {
            return false;
        }

        return hash_equals($localChecksum, $remoteChecksum);
    }

    public function isJson($string)
    {
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
}

