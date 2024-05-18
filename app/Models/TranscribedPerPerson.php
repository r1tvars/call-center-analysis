<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranscribedPerPerson extends Model
{
    use HasFactory;

    protected $table = 'transcribed_per_person';

    protected $fillable = ['call_record_id', 'transcription_html'];

    public function callRecord()
    {
        return $this->belongsTo(CallRecord::class);
    }
}
