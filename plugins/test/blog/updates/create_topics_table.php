<?php namespace Test\Blog\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateTopicsTable extends Migration
{
    public function up()
    {
        Schema::create('test_blog_topics', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->index();
            $table->timestamps();
        });

        Schema::create('test_blog_news_topics', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('news_id')->unsigned();
            $table->integer('topic_id')->unsigned();
            $table->primary(['news_id', 'topic_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_blog_topics');
        Schema::dropIfExists('test_blog_news_topics');
    }
}
