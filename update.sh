#!/bin/sh

git fetch --all
git reset --hard origin/master
curl -sS https://getcomposer.org/installer | php
php composer.phar install

