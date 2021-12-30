<?php namespace Test\Blog\Tests;

use PluginTestCase;
use TestCase;

class NewsTest extends TestCase
{

    public function testCreateFirstPost()
    {
        $response = $this->json('POST', 'api/v1/news', [
            'title' => 'Sally Sendiri',
            'content' => 'Sally Sendiri merupakan sebuah lagu yang dibawakan oleh Peterpan',
            'tags' => 'peterpan,lagu hits indonesia,ariel'
        ]);

        $response->assertStatus(200);
    }

    /**
     * This one gets error 404, but the route's there
     */
    public function testGetAllPublishedPosts()
    {
        $response = $this->get('api/v1/news', [
            'status' => 'published'
        ]);

        $response->assertStatus(200);
    }

    /**
     * This one gets error 500, but the route's there
     */
    public function testCreateFirstTopic()
    {
        $response = $this->json('POST', 'api/v1/topics', [
            'name' => 'Musik',
        ]);

        $response->assertStatus(200);
    }

    /**
     * This one gets error 500, but the route's there
     */
    public function testUpdatePost()
    {
        $update = $this->call('PUT', 'api/v1/news/1', [
            'title' => 'Sally Sendiri',
            'content' => 'Sally Sendiri merupakan sebuah lagu yang dibawakan oleh Peterpan',
            'tags' => 'peterpan,lagu hits indonesia,ariel,noah',
            'is_published' => false,
            'topics_id' => 1
        ]);

        $content = $update->getContent();

        $response = $this->call('GET', 'api/v1/news/' . data_get($content, 'id'));

        $response->assertStatus(200)
            ->assertSee('noah');
    }

    /**
     * This one gets error 500, but the route's there
     */
    public function testDeletePost()
    {
        $response = $this->call('DELETE', 'api/v1/news/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }

    public function testDeleteTopic()
    {
        $response = $this->call('DELETE', 'api/v1/topics/1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    }
}