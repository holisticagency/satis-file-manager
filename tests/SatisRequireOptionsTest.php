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
use holisticagency\satis\utilities\SatisRequireOptions;

/**
 * Require Options Tests.
 *
 * @author James <james@rezo.net>
 */
class SatisRequireOptionsTest extends PHPUnit_Framework_TestCase
{
    protected $require;

    protected function setUp()
    {
        $this->require = new SatisRequireOptions();
    }

    public function testGetDefaultRequireOptions()
    {
        $this->assertTrue($this->require->getAll());
    }

    public function testSerialization()
    {
        $this->require->setAll(false);
        $this->assertEquals(
            'C:50:"holisticagency\satis\utilities\SatisRequireOptions":29:{a:1:{s:11:"require-all";b:1;}}',
            serialize($this->require)
        );
        $this->assertTrue($this->require->getAll());

        $this->assertEquals(
            unserialize('C:50:"holisticagency\satis\utilities\SatisRequireOptions":29:{a:1:{s:11:"require-all";b:1;}}'),
            $this->require
        );

        $this->assertEquals(
            unserialize('C:50:"holisticagency\satis\utilities\SatisRequireOptions":74:{a:2:{s:11:"require-all";b:1;s:8:"wrongkey";s:22:"doSomethingNotExpected";}}'),
            $this->require
        );

        $this->require->setDependencies();
        $this->require->setDevDependencies();
        $this->assertEquals(
            unserialize('C:50:"holisticagency\satis\utilities\SatisRequireOptions":97:{a:3:{s:11:"require-all";b:1;s:20:"require-dependencies";b:1;s:24:"require-dev-dependencies";b:1;}}'),
            $this->require
        );
    }

    public function testBasicSetters()
    {
        $this->require->setAll('SomethingNotBoolean');
        $this->assertTrue($this->require->getAll());

        $this->require->setDependencies(null);
        $this->assertFalse($this->require->getDependencies());
    }

    public function testAddRequire()
    {
        $this->require->setRequire('vendor/name');

        $require = $this->require->getRequire();
        $this->assertEquals('*', $require['vendor/name']);
    }

    /**
     * @depends testAddRequire
     */
    public function testReplaceRequire()
    {
        $this->require->setRequire('vendor/name', '1.0');

        $require = $this->require->getRequire();
        $this->assertEquals('1.0', $require['vendor/name']);
    }

    public function testRemoveRequire()
    {
        $this->require->setRequire('vendor/name');
        $this->require->setRequire('vendor/name', null);

        $require = $this->require->getRequire();
        $this->assertEmpty($require);
    }

    public function testRemoveNotExistingRequire()
    {
        $this->require->setRequire('vendor/name', null);

        $require = $this->require->getRequire();
        $this->assertEmpty($require);
    }

    public function testRequireAllUnset()
    {
        $this->require->setRequire('vendor/name')->setAll(false);
        $this->assertEquals(
            'C:50:"holisticagency\satis\utilities\SatisRequireOptions":53:{a:1:{s:7:"require";a:1:{s:11:"vendor/name";s:1:"*";}}}',
            serialize($this->require)
        );
        $this->assertFalse($this->require->getAll());
    }
}
