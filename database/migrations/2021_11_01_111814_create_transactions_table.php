<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUltimateLibraryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('signhost_signatures', function (Blueprint $table) {
			$table->id();
            $table->string('document_path',255)->index();
			$table->timestamps();
            $table->nullableUuidMorphs('signatureable');
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signhost_signatures');

    }
}
