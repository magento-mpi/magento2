#!/bin/sh -x

chmod 777 ./app/etc
chmod 777 ./media
chmod 777 ./var

find ./media -type f -exec chmod 666 {} \;&& find ./media -type d -exec chmod 777 {} \; 
find ./var -type f -exec chmod 666 {} \;&& find ./var -type d -exec chmod 777 {} \; 
