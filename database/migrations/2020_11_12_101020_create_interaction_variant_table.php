<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteractionVariantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsr_interaction_variant', function (Blueprint $table) {
            // foreign keys
            $table->unsignedBigInteger('interaction_id');
            $table->foreign('interaction_id')
                ->references('id')
                ->on('tsr_interactions')
                ->onDelete('cascade');

            $table->unsignedBigInteger('variant_id');
            $table->foreign('variant_id')
                ->references('id')
                ->on('tsr_variants')
                ->onDelete('cascade');

            // the date and time when that interaction has happened
            // using CURRENT_TIMESTAMP as default just in case
            // this value should be set in PHP for be more precise
            // if value set in PHP queueing can be implemented
            $table->timestamp('moment')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsr_interaction_variant');
    }
}
