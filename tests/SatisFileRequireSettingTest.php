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
use Composer\Package\Package;
use Composer\Package\CompletePackage;

/**
 * Require Setting Tests.
 *
 * @author James <james@rezo.net>
 */
class SatisFileRequireSettingTest extends PHPUnit_Framework_TestCase
{
    protected $satisFile;
    protected $existingConfig;

    protected function setUp()
    {
        $this->satisFile = new SatisFile('http://localhost:54715');
        $this->existingConfig = array(
            'name'  => 'default name',
            'homepage' => 'http://localhost:54715',
            'repositories' => array(),
            'require' => array(
                'vendor/name' => '*',
            ),
            'archive' => array(
                'directory' => 'dist',
            ),
        );
    }

    public function testSetPackageWithExistingConfig()
    {
        $this->satisFile = new SatisFile('http://localhost:54715', $this->existingConfig);

        $require = $this->satisFile->asArray();
        $this->assertEquals(array('vendor/name' => '*'), $require['require']);

        $require = $this->satisFile->getRequireOptions();
        $this->assertEquals(array('vendor/name' => '*'), $require['require']);
    }

    public function PackagesProvider()
    {
        return array(
            array(new Package('vendor/name', '1.0.0.0', '1.0'), '*', array('vendor/name' => '*')), 
            array(new CompletePackage('othervendor/othername', '1.0.0.0', '1.0'), '*', array('othervendor/othername' => '*')), 
            array(new Package('vendor/name', '1.0.0.0', '1.0'), '~1.0', array('vendor/name' => '~1.0')), 
        );
    }

    public function PackagesToRemoveProvider()
    {
        return array(
            array(new Package('vendor/name', '1.0.0.0', '1.0'), true), 
            array(new Package('othervendor/othername', '1.0.0.0', '1.0'), false), 
        );
    }

    /**
     * @dataProvider PackagesProvider
     */
    public function testSetPackages($package, $version, $expected)
    {
        $this->satisFile->setPackage($package, $version);
        $require = $this->satisFile->asArray();
        $this->assertEquals($expected, $require['require']);
    }

    /**
     * @dataProvider PackagesToRemoveProvider
     */
    public function testUnsetPackages($package, $expected)
    {
        $this->satisFile->setPackage(new Package('vendor/name', '1.0.0.0', '1.0'));
        $require = $this->satisFile->asArray();
        echo 'before unset()';
var_dump($require);
var_dump($this->satisFile->getRequireOptions());
ob_flush();
        //$this->assertFalse($require['require-all']);

        $this->satisFile->unsetPackage($package);
        echo 'after unset()';
var_dump($require);
var_dump($this->satisFile->getRequireOptions());
ob_flush();

        $require = $this->satisFile->asArray();
        $this->assertEquals($expected, $require['require-all']);
    }

    public function testManageDependencies()
    {
        $this->satisFile->enableRequireDependencies();
        $require = $this->satisFile->asArray();
        $this->assertTrue($require['require-dependencies']);

        $this->satisFile->disableRequireDependencies();
        $require = $this->satisFile->asArray();
        $this->assertFalse(isset($require['require-dependencies']));

        $this->satisFile->enableRequireDevDependencies();
        $require = $this->satisFile->asArray();
        $this->assertTrue($require['require-dev-dependencies']);

        $this->satisFile->disableRequireDevDependencies();
        $require = $this->satisFile->asArray();
        $this->assertFalse(isset($require['require-dev-dependencies']));
    }
}
