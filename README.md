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

//Create a configuration for a repository located at http://domain.tld
$new = new SatisFile('http://domain.tld');
echo $new->json();

//Add a Vcs repository to an existing configuration
$vcs = new \Composer\Repository\VcsRepository(...);
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated->setRepository($vcs)->asArray());
```

It is not yet full featured. This utility can set vcs, composer or artifact repositories. `\Composer\Repository\PackageRepository` should be implemented. Other options should be created, like the name, requires, web outputs, ...
