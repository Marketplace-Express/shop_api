#!/usr/bin/env bash

source ./utilities/progressbar.sh || exit 1

echo "Done configuring!"

echo "Installing php extensions ..."

i=0
draw_progress_bar $i 6 "extensions"
for ext in intl gettext bcmath zip opcache sockets; do
  docker-php-ext-install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i 6 "extensions"
done
echo

echo "Installing PECL extensions ..."
i=0
draw_progress_bar $i 2 "extensions"
for ext in redis xdebug; do
  echo '' | pecl install ${ext} > /dev/null
  i=$((i+1))
  draw_progress_bar $i 2 "extensions"
done
echo