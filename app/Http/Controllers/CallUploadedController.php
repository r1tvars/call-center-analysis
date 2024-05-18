<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CallRecord;

class CallUploadedController extends Controller
{
    public function index(){

        $cards = CallRecord::all()->map(function($card) {
            // Check if transcription is a string and decode it
            if (is_string($card->transcription)) {
                $card->transcription = json_decode($card->transcription, true);
            }
            return $card;
        });

       return view('uploaded', ['records' => $cards]);

    }

    public function getFirst(int $id){
        $callRecords = CallRecord::findOrFail($id);

        return view('card', ['card' => $callRecords]);
     }


}
