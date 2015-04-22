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
use holisticagency\satis\utilities\SatisWebOutput;

/**
 * Web Output Tests.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisWebOutputTest extends PHPUnit_Framework_TestCase
{
    protected $outputs;

    protected function setUp()
    {
        $this->outputs = new SatisWebOutput();
    }

    public function testGetDefaultWebOptions()
    {
        $this->assertEquals(array(), $this->outputs->get());
    }

    public function testDisableWebOptions()
    {
        $this->outputs->disable();

        $this->assertEquals(array('output-html' => false), $this->outputs->get());
    }

    public function testSetUserDefinedWebOptions()
    {
        $this->outputs->set('/path/to/twig/templates');

        $this->assertEquals(array('twig-template' => '/path/to/twig/templates'), $this->outputs->get());
    }

    public function testSetAllowedExtensions()
    {
        $this->assertEquals(array('html', 'css', 'js'), $this->outputs->getAllowedExtensions());
    }

    public function testSetAllowedUserDefinedExtensions()
    {
        $allowed = $this->outputs->getAllowedExtensions();
        $allowed = array_merge($allowed, array('png'));
        $this->outputs->setAllowedExtensions($allowed);

        $this->assertEquals(array('html', 'css', 'js', 'png'), $this->outputs->getAllowedExtensions());
    }
}
