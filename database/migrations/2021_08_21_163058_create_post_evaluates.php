<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostEvaluates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_evaluates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("post_id")->index();
            $table->unsignedBigInteger("user_evaluate_id")->index();
            $table->mediumText("content")->nullable();
            $table->unsignedTinyInteger("amount_of_start");
            $table->timestamp("created_at")->useCurrent();
            $table->timestamp("updated_at")->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_evaluates');
    }
}
