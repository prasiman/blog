<?php namespace Test\Blog\ApiControllers;

use Cache;
use Exception;
use ApplicationException;
use Test\Blog\Models\Topic;

class TopicController
{

    public function getAllTopics()
    {
        $topicQuery = Topic::query();

        return [
            'success'   => true,
            'data'      => $topicQuery->get()->map(function ($item) {
                return [
                    'id'        => $item->id,
                    'name'      => $item->name,
                    'slug'      => $item->slug
                ];
            })
        ];
    }

    public function getTopicById($id)
    {
        try {
            if (!$item = Topic::find($id)) {
                throw new ApplicationException('No topic found');
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
        $data = \Input::all();

        try {
            Topic::insert([
                'name'      => data_get($data, 'name'),
                'slug'      => str_slug(data_get($data, 'name')),
                'created_at' => now(),
                'updated_at' => now(),
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
        $data = \Input::all();

        if (!$item = Topic::find($id)) {
            throw new ApplicationException('No topic found');
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
        if (!$item = Topic::find($id)) {
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
        if (env('APP_DEBUG')) {
            throw $e;
        } else {
            throw new \ApplicationException('Sistem sedang sibuk, silahkan coba beberapa saat lagi - V001');
        }
    }

}