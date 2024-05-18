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

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tavs uzdevums ir veikt sarunas teksta (call transcript) analīzi un noteikt cik % no sarunas ir ar pozitīvu sentimentu, cik % no sarunas ir ar neitrālu sentimentu un cik % no sarunas ir ar negatīvu sentimentu. Īpaši pievērs uzmanību frāzēm, kur klients piemin: tiesāšos, sūdzēšos tiesā, iesūdzēšu jūs tiesā, sūdzēšos medijiem, vai izsaka citus draudus uzņēmuma pārstāvim.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $text,
                        ],
                    ],
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            Log::info("OpenAI response: " . json_encode($responseBody));

            if (isset($responseBody['choices'][0]['message']['content'])) {
                $analysedText = $responseBody['choices'][0]['message']['content'];
                Log::info("Analysed Text: " . $analysedText);

                // Save the analysed text in the database
                $this->transcribedPerPerson->update(['analysed' => $analysedText]);

                // Verify if the update was successful
                if ($this->transcribedPerPerson->wasChanged('analysed')) {
                    Log::info("Transcription analysis completed and saved successfully.");
                } else {
                    Log::error("Failed to save the analysed text to the database.");
                    Log::info("Analysed Text TYPE: " . gettype($analysedText));
                }
            } else {
                Log::error("OpenAI response missing 'choices' or 'message': " . json_encode($responseBody));
            }
        } catch (\Exception $e) {
            Log::error("Failed to analyze transcription: " . $e->getMessage());
            if ($e->hasResponse()) {
                Log::error("OpenAI API response: " . $e->getResponse()->getBody()->getContents());
            }
        }
    }
}
