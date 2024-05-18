<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\TranscribedPerPerson;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AnalyzeTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transcribedPerPerson;

    /**
     * Create a new job instance.
     *
     * @param  \App\Models\TranscribedPerPerson  $transcribedPerPerson
     * @return void
     */
    public function __construct(TranscribedPerPerson $transcribedPerPerson)
    {
        $this->transcribedPerPerson = $transcribedPerPerson;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = new Client();

        // Strip HTML tags from transcription_html
        $text = strip_tags($this->transcribedPerPerson->transcription_html);

        // Define API URLs
        $apiBase = 'https://api.openai.com/v1';
        $createThreadUrl = "$apiBase/threads";
        $assistantId = "asst_Jsr5L17qU1zOYf3zUYEw1Zn5";

        try {
            // Step 1: Create a Thread
            $threadResponse = $client->post($createThreadUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ],
                'json' => []
            ]);

            $threadResponseBody = json_decode($threadResponse->getBody(), true);
            $threadId = $threadResponseBody['id'];
            Log::info("Thread created with ID: $threadId");

            // Step 2: Add Messages to the Thread
            $addMessageUrl = "$apiBase/threads/$threadId/messages";
            $client->post($addMessageUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ],
                'json' => [
                    'role' => 'user',
                    'content' => $text,
                ],
            ]);
            Log::info("Message added to thread ID: $threadId");

            // Step 3: Create a Run
            $createRunUrl = "$apiBase/assistants/$assistantId/runs";
            $runResponse = $client->post($createRunUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                    'OpenAI-Beta' => 'assistants=v2',
                ],
                'json' => [
                    'thread_id' => $threadId,
                ],
            ]);

            $runResponseBody = json_decode($runResponse->getBody(), true);
            if (isset($runResponseBody['choices'][0]['message']['content'])) {
                $analysedText = $runResponseBody['choices'][0]['message']['content'];
                // Save the analysed text in the database
                $this->transcribedPerPerson->update(['analysed' => $analysedText]);
                Log::info("Transcription analysis completed and saved successfully.");
            } else {
                Log::error("OpenAI response missing 'choices' or 'message': " . json_encode($runResponseBody));
            }
        } catch (\Exception $e) {
            Log::error("Failed to analyze transcription: " . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error("OpenAI API response: " . $e->getResponse()->getBody()->getContents());
            }
        }
    }
}
