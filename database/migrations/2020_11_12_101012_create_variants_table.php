<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ForeignKeyDefinition;
use Illuminate\Support\Facades\Schema;

class CreateVariantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tsr_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key', 50)->unique();
            $table->timestamps();

            // portioning audience
            // 0.1 -> 10%
            // 0.9 -> 90%
            // 0.0 -> disabled, out of portioning
            // null -> enabled to 100% if feature
            $table->decimal('portion', 1, 1)->nullable();

            // relations
            $table->unsignedBigInteger('experiment_id')->nullable();
            $table->foreign('experiment_id')
                ->references('id')
                ->on('tsr_experiments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsr_variants');
    }
}
