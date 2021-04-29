<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelCreatorLabelTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('label_creator_label_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('design');
            $table->text('attributes');
            $table->integer('number_digits');
            $table->integer('columns');
            $table->integer('break_count');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('label_creator_label_types');
    }
}
