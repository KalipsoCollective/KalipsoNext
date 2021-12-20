# KalipsoNext - A simple PHP framework/boilerplate

## Description
A basic php framework/boilerplate. It contains everything that should be in a new structure to save time. You can quickly build your applications on it.

## Requirements
- Apache >= 2.4.5 or Nginx >= 1.8
- PHP >= 7.1
- MySQL >= 5.6

## Documentation
This documentation has been created to give you a quick start.

### Model
(soon)

### View
(soon)

### Controller
(soon)

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
(soon)
