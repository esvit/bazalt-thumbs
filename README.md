bazalt-thumbs
=============
server {
        listen 80;
        server_name ~^(?P<domain>.+)$;

        index index.php;

        # http://whomwah.com/2010/07/05/fixing-the-trailing-slash-in-nginx/
        server_name_in_redirect off;

        root /var/www/sites/davintoo.com/www/public;

        location /static/ {
            root /var/www/sites/davintoo.com/www/public;

            try_files $uri /index.php?file=$uri;
        }
}
