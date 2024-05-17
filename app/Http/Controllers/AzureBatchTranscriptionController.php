<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\CallRecord; // Ensure you have a model for your audio files
use Illuminate\Support\Facades\Log;

class AzureBatchTranscriptionController extends Controller
{
    private $client;
    private $subscriptionKey;
    private $blobEndpoint;
    private $transcriptionEndpoint;

    public function __construct()
    {
        $this->subscriptionKey = env('AZURE_SPEECH_SUBSCRIPTION_KEY');
        $this->blobEndpoint = env('AZURE_STORAGE_BLOB_ENDPOINT');
        $this->transcriptionEndpoint = 'https://westeurope.api.cognitive.microsoft.com/speechtotext/v3.0/transcriptions';
        $this->client = new Client();
    }

    public function startTranscription($fileNames)
    {
        $audioFiles = CallRecord::whereIn('file_name', $fileNames)->where('status', 'notStarted')->get();

        if ($audioFiles->isEmpty()) {
            return response()->json(['message' => 'No files to process'], 404);
        }

        $contentUrls = [];
        foreach ($audioFiles as $audioFile) {
            $contentUrls[] = $this->blobEndpoint . '/' . env('AZURE_STORAGE_CONTAINER_NAME') . '/' . $audioFile->file_name;
        }

        $body = [
            'contentUrls' => $contentUrls,
            'properties' => [
                'diarizationEnabled' => true,
                'wordLevelTimestampsEnabled' => true,
                'punctuationMode' => 'DictatedAndAutomatic',
            ],
            'locale' => 'lv-LV',
            'displayName' => 'Your Transcription Name',
        ];

        try {
            $response = $this->client->post($this->transcriptionEndpoint, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $body,
            ]);

            $responseBody = json_decode($response->getBody(), true);

            // Extract batch_id from the response
            $batchId = basename($responseBody['self']);

            // Update batch_id in the database
            CallRecord::whereIn('file_name', $fileNames)
                ->where('status', 'notStarted')
                ->update(['batch_id' => $batchId]);


            return response()->json(['message' => 'Transcription started successfully', 'data' => $responseBody, 'batch_id' => $batchId], 200);
        } catch (\Exception $e) {
            Log::error('Azure Batch Transcription API request failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to start transcription', 'error' => $e->getMessage()], 500);
        }
    }
}
