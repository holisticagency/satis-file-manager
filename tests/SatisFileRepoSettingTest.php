<?php

/**
 * This file is part of holisatis.
 *
 * (c) Gil <gillesodret@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace holisticagency\satis\Test;

use PHPUnit_Framework_TestCase;
use holisticagency\satis\utilities\SatisFile;
use Composer\Repository\VcsRepository;
use Composer\Repository\PearRepository;
use Composer\Repository\PackageRepository;
use Composer\Repository\ComposerRepository;
use Composer\Repository\PathRepository;
use Composer\Package\Package;
use Composer\IO\NullIO;
use Composer\Config;

/**
 * Setting Tests.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisFileRepoSettingTest extends PHPUnit_Framework_TestCase
{
    protected $satisFile;

    protected function setUp()
    {
        $this->satisFile = new SatisFile('http://localhost:54715');
    }

    public function testUpdatedJson()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile
            ->setRepository($vcsRepository);

        $updatedSatisFile = new SatisFile('http://localhost:54715', $this->satisFile->asArray());

        $vcsNewRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/othervendor/othername.git'),
            new NullIO(),
            new Config()
        );
        $updatedSatisFile
            ->setRepository($vcsNewRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/name.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/othervendor/othername.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $updatedSatisFile->json()
        );
    }

    /**
     * @expectedException     \Exception
     * @expectedExceptionCode 1
     */
    public function testUnsupportedRepository()
    {
        $Repository = new PearRepository(
            array('type' => 'pear', 'url' => 'http://pear2.php.net'),
            new NullIO(),
            new Config()
        );
        $this->satisFile
            ->setRepository($Repository);
    }

    public function testRepositoryNotAddedTwice()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository);

        $vcsNewRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/othervendor/othername.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsNewRepository)
            ->setRepository($vcsRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/name.git"
        },
        {
            "type": "vcs",
            "url": "https://github.com/othervendor/othername.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testRemoveRepository1()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository);

        $this->satisFile = $this->satisFile
            ->unsetRepository($vcsRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testRemoveRepository2()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $vcsOtherRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/othervendor/othername.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository)
            ->setRepository($vcsOtherRepository);

        $this->satisFile = $this->satisFile
            ->unsetRepository($vcsOtherRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/name.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testRemoveRepository3()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $vcsOtherRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/othervendor/othername.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository)
            ->setRepository($vcsOtherRepository);

        $this->satisFile = $this->satisFile
            ->unsetRepository($vcsRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/othervendor/othername.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testDontRemoveUnknownRepository()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $vcsUnknownRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/othervendor/othername.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository);

        $this->satisFile = $this->satisFile
            ->unsetRepository($vcsUnknownRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/name.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testChangeTypeOfRepository()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository);

        $vcsRepository = new VcsRepository(
            array('type' => 'git', 'url' => 'https://github.com/vendor/name.git'),
            new NullIO(),
            new Config()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($vcsRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/vendor/name.git"
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testComposerRepositoryWithSecurityOptions()
    {
        $config = new Config();
        $config->merge(array(
            'config' => array(
                'home' => sys_get_temp_dir().'/composer-home-'.mt_rand().'/',
            ),
        ));
        $ComposerRepository = new ComposerRepository(
            array(
                'type' => 'composer',
                'url' => 'ssh2.sftp://example.org',
                'options' => array(
                    'ssh2' => array(
                        'username' => 'composer',
                        'pubkey_file' => '/home/composer/.ssh/id_rsa.pub',
                        'privkey_file' => '/home/composer/.ssh/id_rsa',
                    ),
                ),

            ),
            new NullIO(),
            $config
        );
        $this->satisFile = $this->satisFile
            ->setRepository($ComposerRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "composer",
            "url": "ssh2.sftp://example.org",
            "options": {
                "ssh2": {
                    "username": "composer",
                    "pubkey_file": "/home/composer/.ssh/id_rsa.pub",
                    "privkey_file": "/home/composer/.ssh/id_rsa"
                }
            }
        }
    ],
    "require-all": true,
    "output-html": false,
    "archive": {
        "directory": "dist"
    }
}',
            $this->satisFile->json()
        );
    }

    public function testPackageRepository()
    {
        $package = array(
            'name' => 'smarty/smarty',
            'version' => '3.1.7',
            'dist' => array(
                'url' => 'http://www.smarty.net/files/Smarty-3.1.7.zip',
                'type' => 'zip',
                'reference' => '',
            ),
            'source' => array(
                'url' => 'http://smarty-php.googlecode.com/svn/',
                'type' => 'svn',
                'reference' => 'tags/Smarty_3_1_7/distribution/',
            ),
            'autoload' => array(
                'classmap' => array('libs/'),
            ),
        );

        $packageConfig = array('type' => 'package', 'package' => $package);
        $Repository = new PackageRepository($packageConfig);
        $this->satisFile
            ->setRepository($Repository);

        $config = $this->satisFile->asArray();
        $PackageRepository = $config['repositories'][0];
        $this->assertEquals(
            $packageConfig,
            $PackageRepository
        );
    }

    public function testPathRepository()
    {
        $realUrl = glob(__DIR__.'/../', GLOB_MARK | GLOB_ONLYDIR);
        $realUrl = $realUrl[0];
        $repository = new PathRepository(
            array('url' => __DIR__.'/../'),
            new NullIO(),
            new Config()
        );
        $this->satisFile
            ->setRepository($repository);

        $config = $this->satisFile->asArray();
        $PathRepository = $config['repositories'][0];
        $this->assertEquals(
            $realUrl,
            $PathRepository['url']
        );
        $this->assertEquals(
            'path',
            $PathRepository['type']
        );
    }
}
