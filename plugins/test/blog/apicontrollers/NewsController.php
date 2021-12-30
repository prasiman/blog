<?php namespace Test\Blog\ApiControllers;

use Cache;
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
                if ($filterStatus == 'draft') {
                    $newsQuery->where('is_published', false)->whereNull('deleted_at');
                } else if ($filterStatus == 'published') {
                    $newsQuery->where('is_published', true)->whereNull('deleted_at');
                } else if ($filterStatus == 'deleted') {
                    $newsQuery->withTrashed()->whereNotNull('deleted_at');
                }
            }

            $cacheName = 'all_news';

            if (Cache::has($cacheName)) {
                $newsData = Cache::get($cacheName);
            }

            $newsData = $newsQuery->get()->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'title'         => $item->title,
                    'slug'          => $item->slug,
                    'created_at'    => date($item->created_at),
                ];
            })->toArray();

            Cache::put($cacheName, $newsData, now()->addMinutes(30));

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
            $cacheName = "news_$slug";

            if (Cache::has($cacheName)) {
                return (array) Cache::get($cacheName);
            }

            if (!$item = News::where('slug', $slug)->with('topics')->first()) {
                return [
                    'success' => false,
                    'message' => 'No post found'
                ];
            }

            $data = [
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

            Cache::put($cacheName, $item, now()->addMinutes(30));

            return $data;
        } catch (Exception $e) {
            return $this->error($e);
        }
    }

    public function createNewPost()
    {
        $data = \Input::all();

        try {
            News::insert([
                'title'     => data_get($data, 'title'),
                'slug'      => str_slug(data_get($data, 'title')),
                'excerpt'   => data_get($data, 'excerpt'),
                'content'   => data_get($data, 'content'),
                'tags'      => json_encode(explode(',', data_get($data, 'tags'))),
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
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
        $data = \Input::all();

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
            $item->is_published = data_get($data, 'is_published');
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
        if (env('APP_DEBUG')) {
            throw $e;
        } else {
            throw new \ApplicationException('Sistem sedang sibuk, silahkan coba beberapa saat lagi - V001');
        }
    }

}