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

/**
 * Archive Setting Tests.
 *
 * @author James <james@rezo.net>
 */
class SatisFileArchiveSettingTest extends PHPUnit_Framework_TestCase
{
    protected $satisFile;

    protected function setUp()
    {
        $config = array(
            'name'  => 'default name',
            'homepage' => 'http://localhost:54715',
            'repositories' => array(),
            'require-all' => true,
            'archive' => array(
                'directory' => 'dist',
                'skip-dev' => false,
            ),
        );
        $this->satisFile = new SatisFile('http://localhost:54715', $config);
    }

    public function testDisableDownloads()
    {
        $this->satisFile->disableArchiveOptions();

        $this->assertEquals(
            $this->satisFile->getArchiveOptions(),
            array()
        );
    }

    public function testInitializationWithExistingConfig()
    {
        $this->assertEquals(
            $this->satisFile->getArchiveOptions(),
            array('directory' => 'dist')
        );
    }

    /**
     * @depends testInitializationWithExistingConfig
     */
    public function testSetArchiveOptions()
    {
        $this->satisFile->setArchiveOptions(array('directory' => 'dist', 'skip-dev' => true));

        $this->assertEquals(
            $this->satisFile->getArchiveOptions(),
            array('directory' => 'dist', 'skip-dev' => true)
        );
    }
}
