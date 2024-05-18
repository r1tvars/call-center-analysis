<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranscribedPerPersonTable extends Migration
{
    public function up()
    {
        Schema::create('transcribed_per_person', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_record_id');
            $table->foreign('call_record_id')->references('id')->on('call_records')->onDelete('cascade');
            $table->text('transcription_html');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transcribed_per_person');
    }
}
