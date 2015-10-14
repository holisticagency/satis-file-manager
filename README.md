# satis-file-manager
Satis configuration file utilities

This library helps to create and update repositories in `satis.json` files.

add the code below to your `composer.json` file :
```json
"require": {
    "holisticagency/satis-file-manager": "~1.0@beta"
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

//A repository may be a Package, an Artifact or a Composer repository:
$packageRepository = new \Composer\Repository\PackageRepository(...);
$artifact = new \Composer\Repository\ArtifactRepository(...);
$composerRepository = new \Composer\Repository\ComposerRepository(...);
$pathRepository = new \Composer\Repository\PathRepository(...);
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated
    ->setRepository($packageRepository)
    ->setRepository($artifact)
    ->setRepository($composerRepository)
    ->setRepository($pathRepository)
    ->asArray()
);

//Remove a repository
var_dump($toBeUpdated->unsetRepository($vcs)->asArray());

//Specify a package
$package = new \Composer\Package\Package(...);
//Or:
$package = new \Composer\Package\CompletePackage(...);

//Specify a package with all its version
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated->setPackage($package)->asArray());

//Specify a package with a specific version (a "pretty" version is expected)
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated->setPackage($package, 'x.y.z')->asArray());

//Nota: The setPackage() methods adds or replaces a Package.

//Remove a specified package
$toBeUpdated = new SatisFile('http://domain.tld', $existingConfig);
var_dump($toBeUpdated->unsetPackage($package)->asArray());

//By default, downloads are enable with zip format and a `dist` directory.
//This does some changes in archive options
$satis->setArchiveOptions(array('directory' => 'downloads', 'skip-dev' => true));
//This disables dist downloads
$satis->disableArchiveOptions();
```
