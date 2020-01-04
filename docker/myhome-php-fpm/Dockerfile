FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-pgsql php-xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN apt-get update \
    && apt-get -y install net-tools software-properties-common apt-utils \
    && add-apt-repository -y ppa:deadsnakes/ppa
    
RUN apt-get update \
    && apt-get -y install python3.6 python3-pip


#RUN chown www-data:www-data -R /var/www/.local
#
RUN usermod -u 1000 www-data
RUN python3.6 -m pip install --upgrade pip
#        
#
RUN pip install python-mirobo
RUN pip install python-miio
RUN export LC_ALL=C.UTF-8 && export LANG=C.UTF-8
#
#RUN mirobo discover



RUN mkdir /var/www
RUN mkdir /var/www/.composer
RUN mkdir /var/www/.composer/cache
RUN chown -R www-data:www-data /var/www/.composer

WORKDIR "/application" 