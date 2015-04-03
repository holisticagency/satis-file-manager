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
use Composer\Repository\ArtifactRepository;
use Composer\Repository\ComposerRepository;
use Composer\IO\NullIO;
use Composer\Config;

/**
 * Base Tests.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisFileInitializationTest extends PHPUnit_Framework_TestCase
{
    protected $satisFile;

    protected function setUp()
    {
        $this->satisFile = new SatisFile('http://localhost:54715');
    }

    public function testBlankInitialization()
    {
        $blankSatisFile = new SatisFile('http://localhost:54715');
        $this->assertEquals(
            array(
                'name'  => 'default name',
                'homepage' => 'http://localhost:54715',
                'repositories' => array(),
                'require-all' => true,
                'archive' => array(
                    'directory' => 'dist',
                    'format' => 'zip',
                ),
                'output-html' => false,
            ),
            $blankSatisFile->asArray()
        );
    }

    public function testInitializationWithArray()
    {
        $satisWithArray = new SatisFile('http://localhost:54715', $this->satisFile->asArray());
        $this->assertEquals(
            array(
                'name'  => 'default name',
                'homepage' => 'http://localhost:54715',
                'repositories' => array(),
                'require-all' => true,
                'archive' => array(
                    'directory' => 'dist',
                    'format' => 'zip',
                ),
                'output-html' => false,
            ),
            $satisWithArray->asArray()
        );
    }

    public function testInitializationWithJson()
    {
        $satisWithArray = new SatisFile('http://localhost:54715', $this->satisFile->json());
        $this->assertEquals(
            array(
                'name'  => 'default name',
                'homepage' => 'http://localhost:54715',
                'repositories' => array(),
                'require-all' => true,
                'archive' => array(
                    'directory' => 'dist',
                    'format' => 'zip',
                ),
                'output-html' => false,
            ),
            $satisWithArray->asArray()
        );
    }

    public function testJsonHasVcs()
    {
        $vcsRepository = new VcsRepository(
            array('type' => 'vcs', 'url' => 'https://github.com/vendor/name.git'),
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
            "type": "vcs",
            "url": "https://github.com/vendor/name.git"
        }
    ],
    "require-all": true,
    "archive": {
        "directory": "dist",
        "format": "zip"
    },
    "output-html": false
}',
            $this->satisFile->json()
        );
    }

    public function testJsonHasArtifact()
    {
        $ArtifactRepository = new ArtifactRepository(
            array('type' => 'artifact', 'url' => 'path/to/artifacts'),
            new NullIO()
        );
        $this->satisFile = $this->satisFile
            ->setRepository($ArtifactRepository);

        $this->assertEquals(
            '{
    "name": "default name",
    "homepage": "http://localhost:54715",
    "repositories": [
        {
            "type": "artifact",
            "url": "path/to/artifacts"
        }
    ],
    "require-all": true,
    "archive": {
        "directory": "dist",
        "format": "zip"
    },
    "output-html": false
}',
            $this->satisFile->json()
        );
    }

    public function testCloningComposerRepository()
    {
        $config = new Config();
        $config->merge(array(
            'config' => array(
                'home' => sys_get_temp_dir().'/composer-home-'.mt_rand().'/',
            ),
        ));
        $ComposerRepository = new ComposerRepository(
            array('type' => 'composer', 'url' => 'http://localhost:43604'),
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
            "url": "http://localhost:43604"
        }
    ],
    "require-all": true,
    "archive": {
        "directory": "dist",
        "format": "zip"
    },
    "output-html": false
}',
            $this->satisFile->json()
        );
    }
}
