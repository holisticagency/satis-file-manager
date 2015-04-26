# satis-file-manager
Satis configuration file utilities

This lirary helps to create and update repositories in `satis.json` files.

add the code below to your `composer.json` file :
```json
"require": {
    "holisticagency/satis-file-manager": "~1.0@alpha"
}
`````

add the code below in a PHP file :
```php
use holisticagency\satis\utilities\SatisFile;

//Create a configuration for a repository located at http://domain.tld
$new = new SatisFile('http://domain.tld');
$new->setName('My Own Private Repository');

//Set minimum stability of the repository
$satis->setStability('stable');

//Set an output directory
$satis->setOutputDir('build');

//Unset the output directory (default)
$satis->unsetOutputDir();

//By default, no web outputs are set.
//This actives the Satis default html output:
$new->setWebOptions(array('output-html' => true));
//Or:
$new->setWebOptions(array('twig-template' => '/path/to/my/twig/templates'));

//Look at the result
echo $new->json();

//Add a Vcs repository to an existing configuration
$vcs = new \Composer\Repository\VcsRepository(...);
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated->setRepository($vcs)->asArray());

//By default, downloads are enable with zip format and a `dist` directory.
//This does some changes in archive options
$satis->setArchiveOptions(array('directory' => 'downloads', 'skip-dev' => true));
//This disables dist downloads
$satis->disableArchiveOptions();
```

It is not yet full featured. This utility can set vcs, composer or artifact repositories. `\Composer\Repository\PackageRepository` should be implemented. Other options should be created, like requires and deps, security options ...
