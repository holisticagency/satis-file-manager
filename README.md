# satis-file-manager
Satis configuration file utilities

This lirary helps to create and update repositories in `satis.json` files.

add the code below to your `composer.json` file :
```json
"require": {
        "holisticagency/satis-file-manager": "~1.0@dev"
}
`````

add the code below in a PHP file :
```php
use holisticagency\satis\utilities\SatisFile;

$file = new SatisFile('http://domain.tld');
echo $file->json();
```

It is not yet full featured. Other options should be created, like the name, requires, ...
