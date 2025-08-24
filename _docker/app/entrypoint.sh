#!/bin/sh
umask 0000
exec php-fpm -F
