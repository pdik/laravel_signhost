<?php

namespace pdik\signhost;

use pdik\signhost\dto\ScribbleVerification;
use pdik\signhost\dto\Signer;
use pdik\signhost\dto\Transaction;

class Signhost
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

    private $client;

    /**
     * @param  string  $appKey
     * @param  string  $apiKey
     * @param  string  $sharedSecret
     * @param  string  $apiEndpoint
     */
    function __construct()
    {
        $this->AppKey = config('signhost_config.app_key');
        $this->ApiKey = config('signhost_config.user_token');
        $this->SharedSecret = config('signhost_config.secret_key');
        $this->ApiEndpoint = config('signhost_config.api_endpoint');
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

    /**
     * Creates a new transaction.
     * @param  Transaction  $transaction
     * @return Response
     */
    public function CreateTransaction($transaction)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Content-Type: application/json",
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Gets an existing transaction by providing a transaction ID.
     *
     * When the response has a status code of 410, you can still retrieve
     * partial historical data from the JSON in the error message property.
     * @param  string  $transactionId
     * @return Response
     */
    public function GetTransaction($transactionId)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Deletes an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     */
    public function DeleteTransaction($transactionId)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Length: 0",
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Starts an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     */
    public function StartTransaction($transactionId)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/start");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Length: 0",
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Add a file to an existing transaction by providing a file path
     * and a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @param  string  $filePath
     * @return Response
     */
    public function AddOrReplaceFile($transactionId, $fileId, $filePath)
    {
        $checksum_file = base64_encode(pack('H*', hash_file('sha256', $filePath)));
        $fh = fopen($filePath, 'r');
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
        curl_setopt($ch, CURLOPT_PUT, 1);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Content-Type: application/pdf",
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
            "Digest: SHA256=".$checksum_file,
        ));

        $response = new Response($ch);
        curl_close($ch);
        fclose($fh);

        return $response;
    }

    /**
     * Gets the document of a transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @return Response
     */
    public function GetDocument($transactionId, $fileId)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: ".
            "application/pdf, ".
            "application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Adds file metadata for a file to an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @param  FileMetadata  $metadata
     * @return Response
     */
    public function AddOrReplaceMetadata($transactionId, $fileId, $metadata)
    {
        $ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($metadata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Content-Type: application/json",
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Gets the receipt of a finished transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     */
    public function GetReceipt($transactionId)
    {
        $ch = curl_init($this->ApiEndpoint."/file/receipt/".$transactionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: ".
            "application/pdf, ".
            "application/vnd.signhost.".self::API_VERSION."+json",
            "User-Agent: Signhost PHP Client/".self::CLIENT_VERSION,
            "Application: APPKey ".$this->AppKey,
            "Authorization: APIKey ".$this->ApiKey,
        ));

        $response = new Response($ch);
        curl_close($ch);

        return $response;
    }

    public function SetupScribbleTransaction(array $documentsigners, string $signMessage = 'Could you please sign this document', bool $SignRequest = false)
    {
        $signers = [];
        foreach ($documentsigners as $signer) {
            $scribbleVerification = new ScribbleVerification();
            $scribbleVerification->ScribbleName = $signer['name'];
            $signer = new Signer($signer['email']);
            $signer->SignRequestMessage = $signMessage;
            $signer->SendSignRequest = $SignRequest;
            $signer->Verifications = array($scribbleVerification);
            $signers[] = $signer;
        }

        $transaction = new Transaction();
        $transaction->Signers = $signers;

        return $transaction;
    }


}