<?php

namespace pdik\signhost;

use pdik\signhost\dto\ScribbleVerification;
use pdik\signhost\dto\Signer;
use pdik\signhost\dto\Transaction;
use pdik\signhost\Exception\SignhostException;

class Signhost
{
    /**
     * @var SignhostClient
     */

    private $client;
    private bool $shouldReturnArray;

    /**
     * @var string Defines the secret that can be used to verify file hashes
     */
    private $sharedSecret;

    /**
     * Signhost constructor.
     */
    public function __construct(
        SignhostClient $client,
        bool $shouldReturnArray,
        $sharedSecret
    ) {
        $this->sharedSecret = $sharedSecret;
        $this->client = $client;
        $this->shouldReturnArray = $shouldReturnArray;
    }

    /**
     * @throws SignhostException
     */
    private function performRequest($method, $url, $data = null, $filePath = null)
    {
        $response = $this->client->performRequest($url, $method, $data, $filePath);

        $result = @json_decode($response, $this->shouldReturnArray);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SignhostException('Invalid JSON returned: '.json_last_error_msg());
        }
        return $result;
    }

    /**
     * Returns the response directly (should be a Binary/PDF)
     * @param $method
     * @param $url
     * @param  null  $data
     * @param  null  $filePath
     * @return mixed
     * @throws SignhostException
     */
    private function performBinaryRequest($method, $url, $data = null, $filePath = null)
    {
        $response = $this->client->performRequest($url, $method, $data, $filePath);
        return $response;
    }

    /**
     * Creates a new transaction.
     * @param  Transaction  $transaction
     * @return Response
     * @throws SignhostException
     */
    public function CreateTransaction($transaction)
    {
        return $this->performRequest(
            'POST',
            '/transaction',
            $transaction
        );
    }

    /**
     * Gets an existing transaction by providing a transaction ID.
     *
     * When the response has a status code of 410, you can still retrieve
     * partial historical data from the JSON in the error message property.
     * @param  string  $transactionId
     * @return Response
     * @throws SignhostException
     */
    public function GetTransaction($transactionId)
    {
        return $this->performRequest('GET', '/transaction/'.$transactionId);
    }

    /**
     * Deletes an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     * @throws SignHostException
     */
    public function deleteTransaction($transactionId)
    {
        return $this->performRequest('DELETE', '/transaction/'.$transactionId);
    }

    /**
     * Starts an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     * @throws SignHostException
     */
    public function startTransaction($transactionId)
    {
        $response = $this->client->performRequest("/transaction/".$transactionId."/start", "PUT");

        return json_decode($response, $this->shouldReturnArray);
    }

    /**
     * Add a file to an existing transaction by providing a file path
     * and a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @param  string  $filePath
     * @return Response
     * @throws SignHostException
     */
    public function addOrReplaceFile($transactionId, $fileId, $filePath)
    {
        return $this->performRequest(
            'PUT',
            "/transaction/$transactionId/file/".rawurlencode($fileId),
            null,
            $filePath
        );
    }

    /**
     * Gets the document of a transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @return Response
     * @throws SignHostException
     */
    public function getDocument($transactionId, $fileId)
    {
        return $this->performBinaryRequest(
            'GET',
            "/transaction/$transactionId/file/".rawurlencode($fileId)
        );
    }

    /**
     * Adds file metadata for a file to an existing transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @param  string  $fileId
     * @param  FileMetadata  $metadata
     * @return Response
     * @throws SignHostException
     */
    public function addOrReplaceMetadata($transactionId, $fileId, $metadata)
    {
        return $this->performRequest(
            'PUT',
            "/transaction/$transactionId/file/".rawurlencode($fileId),
            $metadata
        );
    }

    /**
     * Gets the receipt of a finished transaction by providing a transaction ID.
     * @param  string  $transactionId
     * @return Response
     * @throws SignHostException
     */
    public function getReceipt($transactionId)
    {
        return $this->performBinaryRequest(
            'GET',
            "/file/receipt/$transactionId"
        );
    }

    public function SetupScribbleTransaction(
        array $documentsigners,
        string $signMessage = 'Could you please sign this document',
        bool $SignRequest = false
    ) {
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

    public function validateChecksum(
        string $masterTransactionId,
        string $fileId,
        string $status,
        string $remoteChecksum
    ): bool {
        $localChecksum = sha1("$masterTransactionId|$fileId|$status|{$this->sharedSecret}");
        return hash_equals($localChecksum, $remoteChecksum);
    }

}