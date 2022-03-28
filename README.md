# KalipsoNext - A simple PHP framework/boilerplate

## Description
A basic php framework/boilerplate. You can quickly build your applications on it.

## Requirements
- Apache >= 2.4.5 or Nginx >= 1.8
- PHP >= 7.1
- MySQL >= 5.6

## Documentation
This documentation has been created to give you a quick start.

### Tricks

#### Server Configurations (Apache)
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

#### Server Configurations (Nginx)
```nginx_conf
location / {
	if (!-e $request_filename){
		rewrite ^(.+)$ /index.php/$1 break;
	}
}
```

### Routing
You can refer to the examples in resources/route.php for adding new routes.

For example;
```php
'/account/login'    => [
    'middlewares'   => ['Auth@with' => ['nonAuth'], 'CSRF@validate' => ['POST']],
    'controller'    => 'UserController@login'
],
```

- Middlewares: You can assign middleware definitions more than once as an array. You can define the parameters to be sent to the middleware in the form of `key => value`.
- Controller: The controller definition is specified as `class@method` and its counterpart is located in the app/controllers directory.

### Skeleton

- app/
    - config/ _(This folder contains your project settings files. You can use all available settings as in other frameworks. If you are sure that there is a `use App\Helpers\KN;` definition in the relevant file, you can use it as `KN::config('file_name.settings_key')`.)_
    - controllers/ _(Route and routines...)_
    - core/ _(This directory contains the main core of the system. If possible, do not touch at all. Please contribute if you need to touch it and it's a bug.)_
        - DB.php: (Main database class. We used the PDOx class in db layer. Check the [documentation](https://github.com/izniburak/pdox/blob/master/DOCS.md "PDOx Documentation"))
        - Exception.php: (Basic exception handler)
        - Log.php: (Logger.)
        - Notification.php: (Basin notification system layout.)
        - Route.php: (Basic route parser.)
        - System.php: (Main methods.)
    - helpers/ _(You can place helper classes here. Be careful not to delete the `KN` class. It is used in many parts of the system. Many of the methods in it are of such a nature as to prevent you from rewriting.)_
    - middlewares/ _(By default there is CSRF token controller and Auth controller, you can add your other middleware classes here.)_
    - model/ _(You can specify your model layer classes here.)_

- AppController: It is the main controller of the system. It includes pre-definition for sandbox and dynamic JS part. Other examples are provided for ease of use only, you can change or delete them as you wish.
