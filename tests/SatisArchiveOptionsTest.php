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
use holisticagency\satis\utilities\SatisArchiveOptions;

/**
 * Archive Options Tests.
 *
 * @author James <james@rezo.net>
 */
class SatisArchiveOptionsTest extends PHPUnit_Framework_TestCase
{
    protected $archive;

    protected function setUp()
    {
        $this->archive = new SatisArchiveOptions();
    }

    public function testGetDefaultArchiveOptions()
    {
        $this->assertEquals(array(), $this->archive->get());
    }

    public function testRequiredMissingArchiveOptions()
    {
        $this->archive->set(array('format' => 'tar'));

        $this->assertEquals(array(), $this->archive->get());
    }

    public function testUnallowedArchiveOptions()
    {
        $this->archive->set(array('format' => 'exe'));

        $this->assertEquals(array(), $this->archive->get());
    }

    public function testUnknownArchiveOptions()
    {
        $this->archive->set(array('directory' => '', 'url' => ''));

        $this->assertEquals(array(), $this->archive->get());
    }

    public function testMinimalArchiveOptions()
    {
        $this->archive->enable();

        $this->assertEquals(array('archive' => array('directory' => '')), $this->archive->get());
    }

    public function testMinimalArchiveOptions2()
    {
        $this->archive->set(array('directory' => 'dist'));

        $this->assertEquals(array('archive' => array('directory' => 'dist')), $this->archive->get());
    }

    public function testSerialization()
    {
        $this->archive->set(array('directory' => 'dist'));

        $this->assertEquals(
            'C:50:"holisticagency\satis\utilities\SatisArchiveOptions":33:{a:1:{s:9:"directory";s:4:"dist";}}',
            serialize($this->archive)
        );       

        $this->assertEquals(
            unserialize('C:50:"holisticagency\satis\utilities\SatisArchiveOptions":33:{a:1:{s:9:"directory";s:4:"dist";}}'),
            $this->archive
        );

        $this->archive = unserialize('C:50:"holisticagency\satis\utilities\SatisArchiveOptions":56:{a:2:{s:9:"directory";s:4:"dist";s:6:"format";s:3:"exe";}}');
        $this->assertEquals(
            array(),
            $this->archive->get()
        );
    }

    public function testCleanDefaultFormatOption()
    {
        $this->archive->set(array('directory' => 'dist', 'format' => 'zip'));

        $this->assertEquals(array('archive' => array('directory' => 'dist')), $this->archive->get());
    }
}
