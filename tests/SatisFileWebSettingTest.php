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
 * Web Setting Tests.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisFileWebSettingTest extends PHPUnit_Framework_TestCase
{
    protected $satisFile1;
    protected $satisFile2;

    protected function setUp()
    {
        $config = array(
            'name'  => 'default name',
            'homepage' => 'http://localhost:54715',
            'repositories' => array(),
            'require-all' => true,
            'archive' => array(
                'directory' => 'dist',
                'format' => 'zip',
            ),
            'twig-template' => '/path/to/twig/templates',
        );
        $this->satisFile1 = new SatisFile('http://localhost:54715', $config);
        $this->satisFile2 = new SatisFile('http://localhost:54715', $config);
    }

    public function testInitializationWithTwigOption()
    {
        $webConfig = $this->satisFile1->getWebOptions()->get();
        $this->assertEquals(
            '/path/to/twig/templates',
            $webConfig['twig-template']
        );
    }

    public function testInitializationWithOutputOption()
    {
        $this->satisFile2->setWebOptions(array('output-html' => false));

        $webConfig = $this->satisFile2->getWebOptions()->get();
        $this->assertFalse($webConfig['output-html']);

        $this->satisFile2->setWebOptions(array('twig-template' => '/path/to/twig/templates'));

        $webConfig = $this->satisFile2->getWebOptions()->get();
        $this->assertEquals(
            '/path/to/twig/templates',
            $webConfig['twig-template']
        );
    }
}
