#!/usr/bin/env bash

# Generate secret key
#php artisan key:generate -q -n

# Copy PHP extensions configurations to container
cp -a php_extensions/. /usr/local/etc/php/conf.d/
exec "$@"