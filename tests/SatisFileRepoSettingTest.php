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
use Composer\Repository\PackageRepository;
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
        $config = array('package' => new Package('vendor/name', '1.0.0', '1.0.0'));
        $Repository = new PackageRepository($config);
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
}
