<?php

declare(strict_types = 1);

namespace Tests\_fixtures;

use Amondar\Markdown\Markdown;
use Amondar\Postman\Auth\Basic;
use Amondar\Postman\Auth\Bearer;
use Amondar\Postman\Auth\OAuthClientCredentials;
use Amondar\Postman\Auth\OAuthCodeWithPKCE;
use Amondar\Postman\Auth\OAuthPassword;
use Illuminate\Routing\Router;
use Tests\_fixtures\controllers\requests\HomePageRequest;
use Tests\_fixtures\description\MarkdownDescription;

/**
 * Class Routes
 *
 * @author Amondar-SO
 */
class Routes
{
    public static function define(Router $router)
    {
        $router->get('/home', function (HomePageRequest $request) {
            return 'Home';
        })
            ->middleware('web')
            ->name('home')
            ->alias('Home page');

        $router->get('api/v1/users', controllers\UsersListController::class)
            ->name('api.v1.users.list')
            ->middleware(['api', 'auth:api'])
            ->auth(Basic::make('my', 'user'))
            ->alias('Users list')
            ->description('>Here is a simple inline markdown description');

        $router->get('api/v2/posts', [ controllers\PostController::class, 'index' ])
            ->name('api.v1.posts.list')
            ->middleware(['api', 'auth:api'])
            ->auth(Bearer::make('my'))
            ->alias('Posts list')
            ->description(MarkdownDescription::class);

        $router->get('api/v2/posts/{post}', 'Tests\_fixtures\controllers\PostController@show')
            ->name('api.v1.posts.show')
            ->middleware(['api', 'auth:api'])
            ->auth(OAuthClientCredentials::make('id', 'secret'))
            ->alias('Show one post')
            ->description(Markdown::make()->heading('Some heading')->line('Some line'));

        $router->post('api/v2/posts', 'Tests\_fixtures\controllers\PostController@store')
            ->name('api.v1.posts.store')
            ->middleware(['api', 'auth:api'])
            ->auth(OAuthPassword::make(
                'id',
                'secret',
                'my',
                'user',
                scopes: [ 'and', 'scopes' ]
            ))
            ->alias('Create a post');

        $router->match([ 'put', 'patch' ], 'api/v2/posts/{post}', 'Tests\_fixtures\controllers\PostController@update')
            ->name('api.v1.posts.update')
            ->middleware(['api', 'auth:api'])
            ->auth(OAuthPassword::make(
                username: 'my',
                password: 'user',
                scopes: [ 'and', 'scopes' ]
            ))
            ->alias('Update a post')
            ->description(new MarkdownDescription);

        $router->delete('api/v2/posts/{post}', 'Tests\_fixtures\controllers\PostController@delete')
            ->name('api.v1.posts.delete')
            ->middleware(['api', 'auth:api'])
            ->auth(OAuthPassword::make(scopes: [ 'and', 'scopes' ]))
            ->alias('Delete a post');

        $router->auth(OAuthCodeWithPKCE::make(
            '{{base_url}}/auth/v1/callback',
            '{{base_url}}/oauth/authorize',
            '{{base_url}}/oauth/token',
            scopes: [ 'and', 'scopes' ],
            state: 'my-state',
            clientId: 'my-client-id',
            tokenName: 'My token name here',
        ))
            ->additionalHeaders([ 'X-Custom-Header' => 'my-custom-header' ])
            ->structureDepth(0)
            ->middleware(['api', 'auth:api'])
            ->prefix('api/v2')
            ->as('api.v2.')
            ->group(function () use ($router): void {
                $router->get('users', controllers\UsersListController::class)
                    ->name('users.list')
                    ->alias('Users list V2')
                    ->description('>Here is a simple inline markdown description');
            });
    }
}
