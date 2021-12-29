<?php namespace Test\Blog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('test_blog_news', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('slug')->index();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->longText('tags')->nullable();
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_blog_news');
    }
}
