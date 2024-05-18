<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\CallRecord;
use App\Models\TranscribedPerPerson;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Jobs\AnalyzeTranscription;

class CheckTranscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batchId;

    /**
     * Create a new job instance.
     *
     * @param  string  $batchId
     * @return void
     */
    public function __construct($batchId)
    {
        $this->batchId = $batchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();
        $transcriptionEndpoint = 'https://westeurope.api.cognitive.microsoft.com/speechtotext/v3.0/transcriptions/' . $this->batchId;

        try {
            $response = $client->get($transcriptionEndpoint, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => env('AZURE_SPEECH_SUBSCRIPTION_KEY'),
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $status = $responseBody['status'];

            switch ($status) {
                case 'Succeeded':
                    // Second GET request to fetch the files link
                    $filesLink = $responseBody['links']['files'];
                    $filesResponse = $client->get($filesLink, [
                        'headers' => [
                            'Ocp-Apim-Subscription-Key' => env('AZURE_SPEECH_SUBSCRIPTION_KEY'),
                            'Content-Type' => 'application/json',
                        ],
                    ]);

                    $filesResponseBody = json_decode($filesResponse->getBody(), true);
                    if (!empty($filesResponseBody['values']) && isset($filesResponseBody['values'][0]['links']['contentUrl'])) {
                        $contentUrl = $filesResponseBody['values'][0]['links']['contentUrl'];

                        // Third GET request to fetch the transcription content
                        $contentResponse = $client->get($contentUrl, [
                            'headers' => [
                                'Ocp-Apim-Subscription-Key' => env('AZURE_SPEECH_SUBSCRIPTION_KEY'),
                                'Content-Type' => 'application/json',
                            ],
                        ]);

                        $transcriptionContent = $contentResponse->getBody()->getContents();

                        // Encode the transcription content as JSON before saving
                        if (is_string($transcriptionContent)) {
                            $transcriptionContentJson = json_decode($transcriptionContent, true);
                        } else {
                            $transcriptionContentJson = $transcriptionContent;
                        }

                        $callRecord = CallRecord::where('batch_id', $this->batchId)->first();
                        if ($callRecord) {
                            $callRecord->update(['status' => 'Succeeded', 'transcription' => $transcriptionContentJson]);

                            // Process the transcription JSON and save the HTML markup to the new table
                            $transcribedPerPerson = $this->processAndSaveTranscription($callRecord, $transcriptionContentJson);

                            // Dispatch job to analyze the transcription
                            if ($transcribedPerPerson) {
                                // Dispatch job to analyze the transcription
                                AnalyzeTranscription::dispatch($transcribedPerPerson);
                            }
                        }

                        Log::info("Transcription batch {$this->batchId} completed and content saved successfully.");
                    } else {
                        Log::error("Transcription batch {$this->batchId} succeeded but content URL not found.");
                    }
                    break;

                case 'Running':
                    // Update the status of call records in the batch
                    CallRecord::where('batch_id', $this->batchId)->update(['status' => 'Running']);
                    // Re-dispatch the job to check again after some time
                    CheckTranscriptionStatus::dispatch($this->batchId)->delay(now()->addSeconds(30));
                    break;

                default:
                    // Re-dispatch the job to check again after some time
                    CheckTranscriptionStatus::dispatch($this->batchId)->delay(now()->addSeconds(30));
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Failed to check transcription status for batch {$this->batchId}: " . $e->getMessage());
        }
    }

    /**
     * Process and save the transcription data as HTML
     *
     * @param  \App\Models\CallRecord  $callRecord
     * @param  array  $transcriptionData
     * @return \App\Models\TranscribedPerPerson|null
     */
    private function processAndSaveTranscription(CallRecord $callRecord, array $transcriptionData)
    {
        $htmlContent = '';

        if (isset($transcriptionData['recognizedPhrases'])) {
            foreach ($transcriptionData['recognizedPhrases'] as $phrase) {
                $speaker = $phrase['speaker'];
                $text = $phrase['nBest'][0]['display'];
                $htmlContent .= "<p>Speaker $speaker: $text</p>";
            }

            return TranscribedPerPerson::create([
                'call_record_id' => $callRecord->id,
                'transcription_html' => $htmlContent,
            ]);
        }

        return null; // Handle the case where no transcription data is available
    }
}
