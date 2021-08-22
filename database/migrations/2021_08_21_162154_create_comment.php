<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("parent_id")->index();
            $table->unsignedBigInteger("post_id")->index();
            $table->unsignedBigInteger("user_id")->index();
            $table->string("ip_sender", 50)->nullable();
            $table->mediumText("content");
            $table->unsignedBigInteger("amount_of_like")->default(0);
            $table->unsignedBigInteger("amount_of_unlike")->default(0);
            $table->boolean("display");
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
        Schema::dropIfExists('comment');
    }
}
