<?php

Route::group([
    'prefix' => env('API_PREFIX', 'api') . '/v1',
    'namespace' => 'Test\Blog\ApiControllers',
], function () {

    // News
    Route::get('news', 'NewsController@getAllNews');
    Route::get('news/{id}', 'NewsController@getNewsById');
    Route::post('news', 'NewsController@createNewPost');
    Route::put('news/{id}', 'NewsController@updateNews');
    Route::delete('news/{id}', 'NewsController@deleteNews');

    // Topic
    Route::get('topics', 'TopicController@getAllTopics');
    Route::get('topics/{id}', 'TopicController@getTopicById');
    Route::post('topics', 'TopicController@createNewTopic');
    Route::put('topics/{id}', 'TopicController@updateTopic');
    Route::delete('topics/{id}', 'TopicController@deleteTopic');

});