<?php namespace Test\Blog\ApiControllers;

use Exception;
use Test\Blog\Models\Topic;

class TopicController
{

    public function getAllTopics()
    {
        $topicQuery = Topic::query();

        return $topicQuery->get()->map(function ($item) {
            return [
                'id'        => $item->id,
                'name'      => $item->name,
                'slug'      => $item->slug
            ];
        });
    }

    public function getTopicBySlug($slug)
    {
        try {
            if (!$item = Topic::where('slug', $slug)->first()) {
                return [
                    'success' => false,
                    'message' => 'No topic found'
                ];
            }

            return [
                'success'   => true,
                'data'      => [
                    'id'            => $item->id,
                    'name'          => $item->name,
                    'slug'          => $item->slug,
                    'news'          => $item->news->map(function ($news) {
                        return [
                            'id'            => $news->id,
                            'title'         => $news->title,
                            'slug'          => $news->slug,
                            'created_at'    => date($news->created_at),
                        ];
                    }),
                    'created_at'    => date($item->created_at),
                    'updated_at'    => date($item->updated_at),
                ]
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function createNewTopic()
    {
        $data = post();

        try {
            Topic::insert([
                'name'      => data_get($data, 'name'),
                'slug'      => str_slug(data_get($data, 'name')),
            ]);

            return [
                'success'   => true,
                'message'   => 'New topic successfully created!'
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function updateTopic($id)
    {
        $data = post();

        if (!$item = Topic::find($id)) {
            return [
                'success' => false,
                'message' => 'No topic found'
            ];
        }

        try {
            $item->name = data_get($data, 'name');
            $item->slug = str_slug(data_get($data, 'name'));
            $item->save();

            return [
                'success'   => true,
                'message'   => 'Topic successfully updated!'
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function deleteTopic($id)
    {
        if (!$item = topic::find($id)) {
            return [
                'success' => false,
                'message' => 'No topic found'
            ];
        }

        try {
            $item->delete();

            return [
                'success'   => true,
                'message'   => 'Topic successfully deleted!'
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    private function error(Exception $e)
    {
        return [
            'success'   => false,
            'message'   => $e->getMessage()
        ];
    }

}