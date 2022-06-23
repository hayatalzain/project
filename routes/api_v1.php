<?php

    Route::get('/swagger', function (){
        $swagger = \Swagger\scan(base_path() . '/app');
        return $swagger;
    });


    Route::get('/', function () {

      // return response(null, 204);

       return "welcome v1" ;
    });



    Route::post('/login', ['as' => 'auth.login', 'uses' => 'AuthController@login']); // login user

    Route::post('/signup', ['as' => 'auth.signup', 'uses' => 'AuthController@signup']); // register user

    Route::post('/password/email','ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'ResetPasswordController@resetPassword');


    // Route::post('me/sendPassword',['as' => 'auth.sendPassword', 'uses' => 'AuthController@sendPassword']); // Send Password user
    // Route::post('me/resetPassword',['as' => 'auth.resetPassword', 'uses' => 'AuthController@resetPassword']); // Reset Password user

    // Route::resource('news','NewsPaperController')->only(['index'
    //  ]);


    Route::get('/version', 'GeneralController@version');
    Route::get('/settings'  ,'GeneralController@settings');
    Route::get('/walkthroughs'  ,'GeneralController@walkthroughs');
    Route::get('/pages', 'GeneralController@pages');
    Route::get('/pages/{id}', 'GeneralController@page');

    Route::get('/profile/{uuid}/', 'ProfileController@show')->name('user.profile')->where('uuid','[0-9]+');


    // Auth Routes
  Route::middleware('auth:api')->group(function () {


    Route::get('/profile/{profileId}/posts', 'PostsController@GetByProfileId')->name('user.profile.posts');
    Route::get('/users/search','PeopleController@search')->name('api.users.search');
    Route::get('/people/popular','PeopleController@popular')->name('api.users.popular');
    Route::get('/people/mostViews','PeopleController@mostViews');


    Route::get('me/block-list', ['as' => 'block.user', 'uses' => 'ProfileController@blockList']);
    Route::post('profile/{id}/block', ['as' => 'block.user', 'uses' => 'ProfileController@blockUser']);
    Route::post('profile/{id}/unblock/', ['as' => 'block.user', 'uses' => 'ProfileController@UnBlockUser']);

    Route::get('me/mute-list', ['as' => 'mute.user', 'uses' => 'ProfileController@muteList']);
    Route::post('profile/{id}/mute', ['as' => 'mute.user', 'uses' => 'ProfileController@MuteUser']);
    Route::post('profile/{id}/unmute/', ['as' => 'mute.user', 'uses' => 'ProfileController@UnMuteUser']);
    Route::post('profile/infraction/', ['as' => 'infraction.user', 'uses' => 'ProfileController@addInfraction']);

    Route::post('/notification/send', 'GeneralController@sendNotification');


    Route::get('/logout', ['as' => 'auth.logout', 'uses' => 'AuthController@logout']); // login user
    Route::delete('/me/suspended-account', ['as' => 'auth.suspended', 'uses' => 'AuthController@destroy']);
    Route::get('profile/me',['as' => 'auth.me', 'uses' => 'ProfileController@me']);
    Route::patch('profile/me',['as' => 'auth.me', 'uses' =>'AuthController@update']);
    Route::put('profile/me',['as' => 'auth.me', 'uses' => 'AuthController@updateFull']);
    Route::post('profile/upload',['as' => 'auth.profile.upload', 'uses' => 'AuthController@uploadPhoto']);


    Route::post('/profile/follow', 'ProfileController@followUser')->name('user.follow');
    Route::delete('followings/{following_id}/unfollow', 'ProfileController@unFollowUser')->name('user.unfollow');
    Route::get('followers/{user_id}', 'ProfileController@followers')->name('user.followers');
    Route::get('followings/{user_id}', 'ProfileController@followings')->name('user.followings');
    Route::post('friends/rejectFollower', 'ProfileController@rejectFollower')->name('user.list-friends-reject-follower');
    Route::post('friends/acceptFollower', 'ProfileController@acceptFollower')->name('user.list-friends-accept-follower');

    Route::get('list-friends-chat', 'ProfileController@ListFriendsChat')->name('user.list-friends-chat');

    Route::post('friends/updateChatList', 'ProfileController@orderListFriends')->name('user.list-friends-chat-update-order');

    Route::get('/people/suggestions','PeopleController@suggestions');

    Route::post('posts/{id}/like', ['as' => 'post.like', 'uses' => 'LikesController@likePost']);
    Route::post('posts/{id}/addComment', ['as' => 'post.addComment', 'uses' => 'PostsController@addComment']);
    Route::delete('posts/{id}/deleteComment', ['as' => 'post.deleteComment', 'uses' => 'PostsController@destroyComment']);
    Route::post('comments/{id}/like', ['as' => 'comment.like', 'uses' => 'LikesController@likeComment']);
    Route::post('/posts/infraction','PostsController@addInfraction');
    Route::post('/comments/infraction','PostsController@addInfractionComment');

    Route::get('/posts/home','PostsController@home')->name('posts.home');

    Route::resource('posts','PostsController');

    Route::post('/contact-us','GeneralController@storeContact');

    Route::get('messages',['as' => 'auth.me.messages', 'uses' => 'MessagesController@index']);
    Route::get('messages/{id}',['as' => 'auth.me.message', 'uses' => 'MessagesController@show']);
    Route::post('messages',['as' => 'auth.me.messages', 'uses' => 'MessagesController@store']);
    Route::post('messages/{id}/reply/',['as' => 'auth.me.messages.reply', 'uses' => 'MessagesController@addReply']);
    Route::delete('messages/{id}',['as' => 'auth.me.message.destroy', 'uses' => 'MessagesController@destroy']);
    Route::post('/messages/infraction','MessagesController@addInfraction');

    Route::put('/chats','ChatsController@update');
    Route::resource('chats','ChatsController',['only'=>['index','store','destroy']]);


    Route::get('/me/favorites', 'MessagesController@getFavorites');
    Route::post('/me/messages/{id}/favorite', 'MessagesController@setFavorite');
    Route::delete('/me/messages/{id}/favorite', 'MessagesController@unsetFavorite');

    Route::get('me/notifications',['as' => 'auth.me.notifications', 'uses' => 'NotificationsController@index']);
    Route::get('me/notifications/{id}',['as' => 'auth.me.notifications', 'uses' => 'NotificationsController@show']);
    Route::delete('me/notifications/{id}',['as' => 'auth.me.notification.destroy', 'uses' => 'NotificationsController@destroy']);
    Route::get('me/inbox/countUnread',['as' => 'auth.me.inbox.countUnread', 'uses' => 'NotificationsController@CountUnReadInbox']);



});
