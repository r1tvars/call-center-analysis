<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnalysedToTranscribedPerPersonTable extends Migration
{
    public function up()
    {
        Schema::table('transcribed_per_person', function (Blueprint $table) {
            $table->text('analysed')->nullable()->after('transcription_html');
        });
    }

    public function down()
    {
        Schema::table('transcribed_per_person', function (Blueprint $table) {
            $table->dropColumn('analysed');
        });
    }
}
