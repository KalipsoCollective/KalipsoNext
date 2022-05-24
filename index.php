<?php 

/**
 * @package KN
 * @author halillusion <halillusion@gmail.com>
 **/

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/bootstrap.php';

try {

    $app = (new KN\Core\Factory);

    // Multi route group
    $app->routes([
        ['GET,POST', '/sandbox', 'AppController@sandbox'],
        ['GET,POST', '/sandbox/:action', 'AppController@sandbox']
    ]);


    // Root-bound route group
    $app->routeGroup(['GET,POST', '/auth', 'UserController@account', ['Auth@with']], function () {
        return [
            ['GET,POST', '/login', 'UserController@login', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/register', 'UserController@register', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/recovery', 'UserController@recovery', ['Auth@withOut', 'CSRF@validate']],
            ['GET,POST', '/logout', 'UserController@logout', ['Auth@with']],
            ['GET,POST', '/:action', 'UserController@account', ['Auth@with', 'CSRF@validate']],
        ];
    });

    $app->routeGroup(['GET,POST', '/management', 'AdminController@dashboard', ['Auth@with']], function () {
        return [
            // Users
            ['GET,POST', '/users', 'AdminController@users', ['Auth@with']],
            ['GET,POST', '/users/list', 'AdminController@userList', ['Auth@with']],
            ['GET,POST', '/users/add', 'AdminController@userAdd', ['Auth@with']],
            ['GET,POST', '/users/:id', 'AdminController@userDetail', ['Auth@with']],
            ['GET,POST', '/users/:id/delete', 'AdminController@userDelete', ['Auth@with']],
            ['GET,POST', '/users/:id/update', 'AdminController@userUpdate', ['Auth@with']],

            // Roles
            ['GET,POST', '/roles', 'AdminController@roles', ['Auth@with']],
            ['GET,POST', '/roles/list', 'AdminController@roleList', ['Auth@with']],
            ['GET,POST', '/roles/add', 'AdminController@roleAdd', ['Auth@with']],
            ['GET,POST', '/roles/:id', 'AdminController@roleDetail', ['Auth@with']],
            ['GET,POST', '/roles/:id/delete', 'AdminController@roleDelete', ['Auth@with']],
            ['GET,POST', '/roles/:id/update', 'AdminController@roleUpdate', ['Auth@with']],

            // Sessions
            ['GET,POST', '/sessions', 'AdminController@sessions', ['Auth@with']],
            ['GET,POST', '/sessions/list', 'AdminController@sessionList', ['Auth@with']],

            // Logs & Security
            ['GET,POST', '/logs', 'AdminController@logs', ['Auth@with']],
            ['GET,POST', '/logs/list', 'AdminController@logList', ['Auth@with']],
            ['GET,POST', '/logs/:ip/block', 'AdminController@logIpBlock', ['Auth@with']],

            // Pages
            ['GET,POST', '/pages', 'ContentController@pages', ['Auth@with']],
            ['GET,POST', '/pages/list', 'ContentController@pageList', ['Auth@with']],
            ['GET,POST', '/pages/add', 'ContentController@pageAdd', ['Auth@with']],
            ['GET,POST', '/pages/:id', 'ContentController@pageDetail', ['Auth@with']],
            ['GET,POST', '/pages/:id/delete', 'ContentController@pageDelete', ['Auth@with']],
            ['GET,POST', '/pages/:id/update', 'ContentController@pageUpdate', ['Auth@with']],

            // Contents
            ['GET,POST', '/contents', 'ContentController@contents', ['Auth@with']],
            ['GET,POST', '/contents/list', 'ContentController@contentList', ['Auth@with']],
            ['GET,POST', '/contents/add', 'ContentController@contentAdd', ['Auth@with']],
            ['GET,POST', '/contents/:id', 'ContentController@contentDetail', ['Auth@with']],
            ['GET,POST', '/contents/:id/delete', 'ContentController@contentDelete', ['Auth@with']],
            ['GET,POST', '/contents/:id/update', 'ContentController@contentUpdate', ['Auth@with']],

            // Categories
            ['GET,POST', '/categories', 'ContentController@categories', ['Auth@with']],
            ['GET,POST', '/categories/list', 'ContentController@categoryList', ['Auth@with']],
            ['GET,POST', '/categories/add', 'ContentController@categoryAdd', ['Auth@with']],
            ['GET,POST', '/categories/:id', 'ContentController@categoryDetail', ['Auth@with']],
            ['GET,POST', '/categories/:id/delete', 'ContentController@categoryDelete', ['Auth@with']],
            ['GET,POST', '/categories/:id/update', 'ContentController@categoryUpdate', ['Auth@with']],

            // Media
            ['GET,POST', '/media', 'ContentController@media', ['Auth@with']],
            ['GET,POST', '/media/list', 'ContentController@mediaList', ['Auth@with']],
            ['GET,POST', '/media/add', 'ContentController@mediaAdd', ['Auth@with']],
            ['GET,POST', '/media/:id', 'ContentController@mediaDetail', ['Auth@with']],
            ['GET,POST', '/media/:id/delete', 'ContentController@mediaDelete', ['Auth@with']],
            ['GET,POST', '/media/:id/update', 'ContentController@mediaUpdate', ['Auth@with']],

            // Contact
            ['GET,POST', '/forms', 'FormController@forms', ['Auth@with']],
            ['GET,POST', '/forms/list', 'FormController@formList', ['Auth@with']],
            ['GET,POST', '/forms/:id', 'FormController@formDetail', ['Auth@with']],
            ['GET,POST', '/forms/:id/delete', 'FormController@formDelete', ['Auth@with']],
            ['GET,POST', '/forms/:id/update', 'FormController@formUpdate', ['Auth@with']],

            // Services
            ['GET,POST', '/services', 'ServiceController@services', ['Auth@with']],
            ['GET,POST', '/services/list', 'ServiceController@serviceList', ['Auth@with']],
            ['GET,POST', '/services/add', 'ServiceController@serviceAdd', ['Auth@with']],
            ['GET,POST', '/services/:id', 'ServiceController@serviceDetail', ['Auth@with']],
            ['GET,POST', '/services/:id/delete', 'ServiceController@serviceDelete', ['Auth@with']],
            ['GET,POST', '/services/:id/update', 'ServiceController@serviceUpdate', ['Auth@with']],

            // Menus
            ['GET,POST', '/menus', 'MenuController@menus', ['Auth@with']],
            ['GET,POST', '/menus/list', 'MenuController@menuList', ['Auth@with']],
            ['GET,POST', '/menus/add', 'MenuController@menAdd', ['Auth@with']],
            ['GET,POST', '/menus/:id', 'MenuController@menuDetail', ['Auth@with']],
            ['GET,POST', '/menus/:id/delete', 'MenuController@menuDelete', ['Auth@with']],
            ['GET,POST', '/menus/:id/update', 'MenuController@menuUpdate', ['Auth@with']],

            // Widgets
            ['GET,POST', '/widgets', 'WidgetController@widgets', ['Auth@with']],
            ['GET,POST', '/widgets/list', 'WidgetController@widgetList', ['Auth@with']],
            ['GET,POST', '/widgets/add', 'WidgetController@widgetAdd', ['Auth@with']],
            ['GET,POST', '/widgets/:id', 'WidgetController@widgetDetail', ['Auth@with']],
            ['GET,POST', '/widgets/:id/delete', 'WidgetController@widgetDelete', ['Auth@with']],
            ['GET,POST', '/widgets/:id/update', 'WidgetController@widgetUpdate', ['Auth@with']],

            // Settings
            ['GET,POST', '/settings', 'AdminController@settings', ['Auth@with']],
            ['GET,POST', '/settings/update', 'AdminController@settingsUpdate', ['Auth@with']],
        ];
    });

    // Single route
    $app->route('GET', '/', 'AppController@index', ['Auth@verifyAccount']);

    // Do not remove this route for the KN script library.
    $app->route('GET,POST', '/cron', 'AppController@cronJobs');
    $app->route('GET,POST', '/sandbox', 'AppController@sandbox');
    $app->route('GET,POST', '/sandbox/:action', 'AppController@sandbox');

    $app->excludeWhileInMaintenance([
        'auth/login'
    ]);

    $app->run();

} catch (Exception $e) {

    KN\Core\Exception::exceptionHandler($e);

}