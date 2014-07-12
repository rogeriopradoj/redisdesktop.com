#!/bin/sh

git fetch --all
git reset --hard origin/master
git pull
curl -sS https://getcomposer.org/installer | php
php composer.phar install
touch ./public/index.php

