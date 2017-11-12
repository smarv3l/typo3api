FROM chialab/php-dev:7.1-apache

# install dependencies
# libpcre in debian:jessie is too old for typo3 so i need to pin it from stretch
RUN echo deb http://deb.debian.org/debian stretch main >> /etc/apt/sources.list.d/stretch.list \
 && echo "Package: libpcre3" >> /etc/apt/preferences.d/stretch \
 && echo "Pin: release a=stretch" >> /etc/apt/preferences.d/stretch \
 && echo "Pin-Priority: 1000" >> /etc/apt/preferences.d/stretch \
 && echo "Package: *" >> /etc/apt/preferences.d/stretch \
 && echo "Pin: release a=stretch" >> /etc/apt/preferences.d/stretch \
 && echo "Pin-Priority: 1" >> /etc/apt/preferences.d/stretch \
 && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y libpcre3

# change configuration
RUN sed -i "s#/var/www/html#/var/www/web#g" /etc/apache2/sites-enabled/000-default.conf \
 && echo max_execution_time=240 >> /usr/local/etc/php/conf.d/php.ini \
 && echo xdebug.max_nesting_level=400 >> /usr/local/etc/php/conf.d/php.ini \
 && echo max_input_vars=1500 >> /usr/local/etc/php/conf.d/php.ini \
 && echo opcache.enable_file_override=On >> /usr/local/etc/php/conf.d/php.ini