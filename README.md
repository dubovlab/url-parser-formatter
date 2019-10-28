# dubovlab/url-parser-formatter
URL parsing and formatting library


## Usage


### Core functions:
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$str = 'https://user:pass@host:55555/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';

$components = \url\parse($str);

echo $components['scheme'] . '://' . $components['host'] . ':' . $components['port'] . PHP_EOL;

echo \url\format($components) . PHP_EOL; 
```


### Url class:
```php
<?php 

require_once __DIR__ . '/vendor/autoload.php';

$str = '/root/dir/file.ext?query_key=query_value&query_key2=query_value2#fragment_key=fragment_value&fragment_key2=fragment_value2';
$base_url = 'https://user:pass@host:55555';

$url = \url\Url::parse($str, $base_url);

echo $url->scheme . '://' . $url->host . ':' . $url->port . PHP_EOL;

echo $url->format(null, ['scheme', 'host', 'port']) . PHP_EOL;

echo $url->query('query_key2') . PHP_EOL;

echo $url->fragment('fragment_key') . PHP_EOL;

echo $url . PHP_EOL;
```


## Todo list:
* function reference
* normalize_path()
