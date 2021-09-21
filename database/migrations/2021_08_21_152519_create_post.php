<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("category_id")->index();
            $table->unsignedBigInteger("user_id")->index();
            $table->string("title")->index();
            $table->longText("content");
            $table->string("seo_title")->nullable();
            $table->longText("seo_content")->nullable();
            $table->string("thumbnail", 500)->nullable();
            $table->string("link", 500)->nullable();
            $table->string("author")->nullable();
            $table->boolean("display")->default(1);
            $table->boolean("status")->default(0);
            $table->unsignedBigInteger("amount_of_display")->default(0);
            $table->unsignedBigInteger("amount_of_view")->default(0);
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
        Schema::dropIfExists('post');
    }
}
