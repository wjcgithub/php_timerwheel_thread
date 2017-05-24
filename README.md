# php_timerwheel_thread
php多线程版本的时间轮实现

# 环境
php7+

php zts

扩展：
1. pthreads

# 安装
## php7编译安装
```php
./configure --prefix=/usr/local/php715 --with-config-file-path=/usr/local/php715/etc --enable-fpm --with-fpm-user=www --with-fpm-group=www --enable-mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-iconv-dir --with-freetype-dir=/usr/local/freetype --with-jpeg-dir --with-png-dir --with-zlib --with-libxml-dir=/usr --enable-xml --disable-rpath --enable-bcmath --enable-shmop --enable-sysvsem --enable-inline-optimization --with-curl --enable-mbregex --enable-mbstring --enable-intl --with-mcrypt --enable-ftp --with-gd --enable-gd-native-ttf --with-openssl --with-mhash --enable-pcntl --enable-sockets --with-xmlrpc --enable-zip --enable-soap --with-gettext --disable-fileinfo --enable-opcache --with-xsl --enable-maintainer-zts
```

## pthreads　需要在github下载