<?php namespace Test\Blog\ApiControllers;

use Exception;
use Test\Blog\Models\News;

class NewsController
{

    public function getAllNews()
    {
        try {
            $data = \Input::all();

            $newsQuery = News::query();

            if ($filterStatus = data_get($data, 'status')) {
                $newsQuery->where('status', $filterStatus);
            }

            $newsData = $newsQuery->get()->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'title'         => $item->title,
                    'slug'          => $item->slug,
                    'created_at'    => date($item->created_at),
                ];
            });

            return [
                'success'   => true,
                'data'      => $newsData,
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function getNewsBySlug($slug)
    {
        try {
            if (!$item = News::where('slug', $slug)->first()) {
                return [
                    'success' => false,
                    'message' => 'No post found'
                ];
            }

            return [
                'success'   => true,
                'data'      => [
                    'id'            => $item->id,
                    'title'         => $item->title,
                    'slug'          => $item->slug,
                    'excerpt'       => $item->excerpt,
                    'content'       => $item->content,
                    'tags'          => $item->tags,
                    'status'        => $item->status,
                    'topics'        => $item->topics->map(function ($topic) {
                        return [
                            'id'    => $topic->id,
                            'name'  => $topic->name,
                            'slug'  => $topic->slug,
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

    public function createNewPost()
    {
        $data = post();

        try {
            News::insert([
                'title'     => data_get($data, 'title'),
                'slug'      => str_slug(data_get($data, 'title')),
                'excerpt'   => data_get($data, 'excerpt'),
                'content'   => data_get($data, 'content'),
                'tags'      => explode(',', data_get($data, 'tags')),
                'status'    => 'published',
            ]);

            return [
                'success'   => true,
                'message'   => 'New post successfully created!'
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function updateNews($id)
    {
        $data = post();

        if (!$item = News::find($id)) {
            return [
                'success' => false,
                'message' => 'No post found'
            ];
        }

        try {
            $item->title = data_get($data, 'title');
            $item->slug = str_slug(data_get($data, 'title'));
            $item->excerpt = data_get($data, 'excerpt');
            $item->content = data_get($data, 'content');
            $item->tags = explode(',', data_get($data, 'tags'));
            $item->status = data_get($data, 'published');
            $item->save();

            $item->topics()->sync(data_get($data, 'topics_id'));

            return [
                'success'   => true,
                'message'   => 'Post successfully updated!'
            ];
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function deleteNews($id)
    {
        if (!$item = News::find($id)) {
            return [
                'success' => false,
                'message' => 'No post found'
            ];
        }

        try {
            $item->delete();

            return [
                'success'   => true,
                'message'   => 'Post successfully deleted!'
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