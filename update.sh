#!/bin/sh

git pull
curl -sS https://getcomposer.org/installer | php
php composer.phar install

