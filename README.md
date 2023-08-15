> **Warning**
> This repo has been moved to the archive. For updated and modernized versions, please check the single repo at KalipsoCollective/KalipsoX.

# KalipsoNext - A simple PHP framework/boilerplate

## Description
A basic php framework/boilerplate. You can quickly build your applications on it.

## Requirements
- Apache >= 2.4.5 or Nginx >= 1.8
- PHP >= 7.1
- MySQL >= 5.6

## Documentation
This documentation has been created to give you a quick start.

### Installation
- `composer install`
- Visit http://localhost/sandbox
- Prepare DB
- Seed DB
- Start Development

### Tricks

#### Server Configurations (Apache .htaccess)
```htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . index.php [L]
</IfModule>
```

#### Server Configurations (Nginx nginx.conf)
```nginx_conf
location / {
	if (!-e $request_filename){
		rewrite ^(.+)$ /index.php/$1 break;
	}
}
```

### Routing
You can refer to the examples in index.php for adding new routes.

For example;
```php
    $app = (new KN\Core\Factory);

    // Single route
    $app->route('GET', '/', 'AppController@index', ['Auth@verifyAccount']);

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

    // You can use the following method when you want 
    // it to be accessible while in maintenance mode.
    $app->excludeWhileInMaintenance([
        'auth/login'
    ]);

    // Run
    $app->run();
```

! Route methods take the values of request method, request route, controller and middleware in order.

### Skeleton (Understanding KalipsoNext.)

- **app/**
    - **Controllers/** _(Route and routines...)_
        - **AdminController.php**
        _KalipsoNext basically presents the administration panel for you in advance. You can modify and reference all method uses._

        - **AppController.php**
        _It is the main controller of the system. It includes pre-definition for sandbox and other parts. Other examples are provided for ease of use only, you can change or delete them as you wish._

        - **UserController.php**
        _It comes integrated with KalipsoNext basic user module. It includes methods that will save time and can be referenced._

    - **Core/**
        _This directory contains the main core of the system. If possible, do not touch at all. Please contribute if you need to touch it and it's a bug._
        
        - **Auth.php**
        _The core structure of KalipsoNext includes a session module. It is used in integration with other modules. It recommends building your project on it._

        - **Controller.php** 
        _The main controller, from which all controllers are derived, works in conjunction with the Factory class._

        - **Exception.php**
        _KalipsoNext also comes with an exception handler._

        - **Factory.php**
        _It is the core class that handles the processing of requests and their sequential distribution to middleware and controllers._

        - **Log.php**
        _It is a class that logs all requests and responses in the database in case of success or failure, depending on the log setting._

        - **Middleware.php**
         _You should develop your middleware classes by inheriting from this class._

        - **Model.php**
        _You must develop all models by inheriting this class. It includes the PDOx class under the hood. Check the [documentation](https://github.com/izniburak/pdox/blob/master/DOCS.md "PDOx Documentation")_

        - **Notification.php**
        _It is the basic notification class that contains some features for email and in-system notifications._

    - **Helpers/**
        _You can place helper classes here._
        
        - **Base.php**
        _Be careful not to delete the this class. It is used in many parts of the system. Many of the methods in it are of such a nature as to prevent you from rewriting._

        - **KalipsoTable.php**
        _It is the server-side class of the KalipsoTable library used for data listing within the built-in administration panel. Check the [repo](https://github.com/KalipsoCollective/KalipsoTable "KalipsoTable")_

    - **Middlewares/**
    _By default there is CSRF token middleware and Auth middleware, you can add your other middleware classes here._

    - **Model/**
    _You can specify your model layer classes here. If you want features in the core structure, you should not delete the classes in its content._

    - **Resources/**
    _Your main project files are located in this directory._

        - **config/**
        _This folder contains your project settings files. You can use all available settings as in other frameworks. If you are sure that there is a `use KN\Helpers\Base;` definition in the relevant file, you can use it as `Base::config('file_name.setting_key')`. The configuration file associated with the administration panel setting menu is settings.php._

        - **localization/**
        _This directory contains language files. ex: [lang].php_

        - **template/** 
        _This directory contains html template files. ex: email.html_

        - **view/**
        _This directory contains the frontend html files_
            - **_layouts/**
            _This folder consists of php files that hold the string data containing the filenames to be used for the template._ If the view parameter you return from the controller is `['login', 'user']`, it uses the **login** file as the main content file based on the **user** template.

            - **_parts/**
            _You can add partial files given in the template into this file. Like footer.php, header.php or error.php..._

        - **db_schema.php**
        _It is the basic database template file used to prepare tables and import sample data while in sandbox mode._

        - **endpoints.php**
        _It contains the route records used in the user role and authorization control stages._

        - **notifications.php**
        _This is the hook section you will use when developing the file notification system. You can refer to the examples inside._

    - **Storage/**
    _It contains files where data such as cache, log and email content file are stored._

    - **bootstrap.php**
    _It is the file where the error handler and some constants are prepared._

- **assets/**
_Contains interface resource files. CSS, JS, fonts etc._

    - **css/**
        - **kalipso.libs.css**
        _Includes Tabler Icons library and Manrope font definitions._

        - **kalipso.next.css**
        _It is the basic interface style file of KalipsoNext. Contains styles for alerts and for some persistent elements._

    - **fonts/**
        - **manrope/**
        _Manrope font family. Check the [repo](https://github.com/sharanda/manrope "Manrope")_

        - **tabler-icons/**
        _Tabler Icons font family Check the [repo](https://github.com/tabler/tabler-icons "Tabler Icons")._

    - **js/**
        - **kalipso.next.js**
        _It is the basic DOM library for KalipsoNext. It has nice features such as manipulating the interface according to the json outputs sent by the server._

    - **libs/**
        - **bootstrap/**
        _Bootstrap frontend framework. Check the [repo](https://github.com/twbs/bootstrap "Bootstrap")_

        - **kalipsotable/**
        _KalipsoTable data table library. Check the [repo](https://github.com/KalipsoCollective/KalipsoTable "KalipsoTable")_

        - **nprogress/**
        _Nprogress page loader library. Check the [repo](https://github.com/rstacruz/nprogress "NProgress")_

        - **vpjax/**
        _vPjax asynchronous page switch library. Check the [repo](https://github.com/halillusion/vpjax "vPjax")_
