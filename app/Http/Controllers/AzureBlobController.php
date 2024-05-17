<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\Http\Controllers\AzureBatchTranscriptionController;
use App\Jobs\CheckTranscriptionStatus;

use App\Models\CallRecord;

class AzureBlobController extends Controller
{
    private $client;
    private $accountName;
    private $accountKey;
    private $containerName;
    private $blobEndpoint;

    public function __construct()
    {
        $this->accountName = env('AZURE_STORAGE_ACCOUNT_NAME');
        $this->accountKey = env('AZURE_STORAGE_ACCOUNT_KEY');
        $this->containerName = env('AZURE_STORAGE_CONTAINER_NAME');
        $this->blobEndpoint = env('AZURE_STORAGE_BLOB_ENDPOINT');
        $this->client = new Client();
    }

    public function upload(Request $request)
    {
        $request->validate([
            'files.*' => 'required|mimes:mp3,wav,ogg|max:20480', // 20MB max size
        ]);

        $fileNames = [];

        foreach ($request->file('files') as $file) {
            $fileName = $file->getClientOriginalName();
            $fileNames[] = $fileName;
            $filePath = $file->getRealPath();
            $fileContent = file_get_contents($filePath);
            $this->uploadToAzureBlob($fileName, $fileContent, $file->getMimeType());
            CallRecord::create([
                'file_name' => $fileName,
                'status' => 'NotStarted'
            ]);

        }

        // Call AzureBatchTranscriptionController
        $azureBatchTranscriptionController = new AzureBatchTranscriptionController();
        $transcriptionResponse = $azureBatchTranscriptionController->startTranscription($fileNames);

        $responseBody = $transcriptionResponse->getData();
        if ($transcriptionResponse->getStatusCode() == 200) {
            $responseBody = $transcriptionResponse->getData();
            CheckTranscriptionStatus::dispatch($responseBody->batch_id)->delay(now()->addSeconds(30));
            return redirect('/uploaded')->with('success', 'Files uploaded and transcription started successfully.')
                                        ->with('transcriptionData', $transcriptionResponse);
        } else {
            return back()->with('error', 'Files uploaded but transcription failed.');
        }
    }

    private function uploadToAzureBlob($fileName, $fileContent, $contentType)
    {
        $url = $this->blobEndpoint . '/' . $this->containerName . '/' . $fileName;
        $date = gmdate('D, d M Y H:i:s T', time());
        $version = '2019-12-12';
        $method = 'PUT';
        $length = strlen($fileContent);

        $headers = [
            'x-ms-blob-type' => 'BlockBlob',
            'Content-Type' => $contentType,
            'Content-Length' => $length,
            'x-ms-date' => $date,
            'x-ms-version' => $version,
        ];

        $signatureString = $method . "\n" .
            "\n" . // Content-Encoding
            "\n" . // Content-Language
            $length . "\n" . // Content-Length
            "\n" . // Content-MD5
            $contentType . "\n" . // Content-Type
            "\n" . // Date
            "\n" . // If-Modified-Since
            "\n" . // If-Match
            "\n" . // If-None-Match
            "\n" . // If-Unmodified-Since
            "\n" . // Range
            'x-ms-blob-type:' . $headers['x-ms-blob-type'] . "\n" .
            'x-ms-date:' . $date . "\n" .
            'x-ms-version:' . $version . "\n" .
            '/' . $this->accountName . '/' . $this->containerName . '/' . $fileName;

        $signature = base64_encode(hash_hmac('sha256', utf8_encode($signatureString), base64_decode($this->accountKey), true));
        $headers['Authorization'] = 'SharedKey ' . $this->accountName . ':' . $signature;

        try {
            $response = $this->client->request('PUT', $url, [
                'headers' => $headers,
                'body' => $fileContent,
            ]);

            if ($response->getStatusCode() == 201) {
                // File uploaded successfully
                return true;
            } else {
                // Handle other status codes if necessary
                return false;
            }
        } catch (RequestException $e) {
            // Handle the exception
            return false;
        }
    }
}
