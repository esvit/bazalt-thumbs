bazalt-thumbs
=============

## Install via composer
```
php composer require bazalt/thumbs
```
Or just add this config to your composer.json
```
{
    "require": {
        "bazalt/thumbs": "dev-master"
    }
}

```

Nginx:
```
location /static/ {
    root /www/public;

    try_files $uri /index.php?file=$uri;
}
```

Apache:
```
RewriteCond %{REQUEST_URI} ^(/static/)
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteRule ^(.*)$ thumb.php?file=$1 [L]
```