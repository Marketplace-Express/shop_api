#!/usr/bin/env bash

source ./utilities/progressbar.sh || exit 1


# Define array of php & pecl extensions
php_extensions=(intl gettext bcmath zip opcache sockets)
pecl_extensions=(redis xdebug)


### START DO NOT EDIT AREA ###
echo "Installing php extensions ..."
php_extensions_count=${#php_extensions[@]}
i=0
draw_progress_bar $i ${php_extensions_count} "extensions"
for ext in ${php_extensions[*]}; do
  docker-php-ext-install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i ${php_extensions_count} "extensions"
done
echo

echo "Installing PECL extensions ..."
pecl_extensions_count=${#pecl_extensions[@]}
i=0
draw_progress_bar $i ${pecl_extensions_count} "extensions"
for ext in ${pecl_extensions[*]}; do
  echo '' | pecl install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i ${pecl_extensions_count} "extensions"
done
echo
### END OF DO NOT EDIT AREA ###