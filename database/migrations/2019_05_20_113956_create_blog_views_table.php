<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_post_views', function (Blueprint $table) {
            $table->unsignedInteger("blog_post_id");
            $table->unsignedInteger("user_id");
            $table->timestamps();

            $table->unique(['blog_post_id', 'user_id']);

            $table->foreign("blog_post_id")
                ->references("id")
                ->on("blog_posts")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            if (Schema::hasTable("users")) {
                $table->foreign("user_id")
                    ->references("id")
                    ->on("users")
                    ->onUpdate("cascade")
                    ->onDelete("cascade");
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("blog_post_views");
    }
}
