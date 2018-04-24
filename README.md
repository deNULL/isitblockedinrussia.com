# isitblockedinrussia.com

This is the source code of isitblockedinrussia.com — a service for checking if a domain or an IP address is currently blocked in Russia. Feel free to reuse it.

== Usage

Information about blocked resourced is downloaded from https://github.com/zapret-info/z-i and stored to MySQL database locally.

Install MySQL and create following tables:
```mysql
CREATE TABLE `blocked` (
  `ip` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_first` int(10) unsigned DEFAULT NULL,
  `ip_last` int(10) unsigned DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `date` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gos_organ` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postanovlenie` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `ip_first` (`ip_first`),
  KEY `ip_last` (`ip_last`),
  KEY `link` (`link`(191)),
  KEY `page` (`page`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `host` varchar(1024) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ips` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_blocked` int(11) DEFAULT NULL,
  `blocks` varchar(4096) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checked_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

Now create directory `config` and place a file named `mysql.php` in it:
```php
<?php
  $db_host = 'localhost';
  $db_user = 'username';
  $db_pass = 'password';
  $db_name = 'database';
```

To download the registry of blocked sites (and store them to 'blocked' table), use `lib/update.php`. You can add it as a cron job, for example (normally it should take about 1 minute). As of April 25, 2018 the table will contain about ~280k rows afterwards.

The web interface is `webroot/index.php`. If you wish to check if a site is blocked from your code, you can include `lib/check.php` and call `checkHost($query)`.
