<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasterTables extends Migration
{
    const SIMPLE_TABLES = [
        'tsr_interactions',
        'tsr_experiments',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::SIMPLE_TABLES as $n) {
            if (!Schema::hasTable($n)) {
                Schema::create($n, function (Blueprint $t) use ($n) {
                    $this->tableDefaults($t);

                    $t->unique('name', "uk_{$n}_name");
                    $t->unique('key', "uk_{$n}_key");
                });
            }
        }

        if (!Schema::hasTable('tsr_variants')) {
            Schema::create('tsr_variants', function (Blueprint $t) {
                $this->tableDefaults($t);

                // represent a percentage of a visitor that should see the variant
                // the range of values that make sense here are greater than 0 and lower tha 1
                $t->decimal('portion', '2', '2');

                // used to attach variant to the experiment, when A/B testing is in place
                // if not it can be used as feature flagging toggle
                $t->unsignedBigInteger('experiment_id')->nullable();
                $t->foreign('experiment_id')
                    ->references('id')
                    ->on('tsr_experiments')
                    ->onDelete('cascade');

                // every experiments variant is required to have a unique key and name
                // to be easily identifiable in the project backend and UI
                $t->unique(['experiment_id', 'name'], 'uk_tsr_variants_name');
                $t->unique(['experiment_id', 'key'], 'uk_tsr_variants_key');
            });
        }

        if (!Schema::hasTable('tsr_records')) {
            Schema::create('tsr_records', function (Blueprint $table) {
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
                // this value should be set in PHP to be more precise
                $table->timestamp('moment')->useCurrent();

                // additional payload
                $table->string('uuid', 36);
                $table->string('url')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tsr_records');
        Schema::dropIfExists('tsr_variants');

        foreach (self::SIMPLE_TABLES as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }

    private function tableDefaults(Blueprint $table)
    {
        $table->bigIncrements('id');
        $table->string('name', 150);
        $table->string('key', 50);
        $table->timestamps();
    }
}
