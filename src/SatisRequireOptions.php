<?php

/**
 * This file is part of holisatis.
 *
 * (c) Gil <gillesodret@users.noreply.github.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace holisticagency\satis\utilities;

use Serializable;

/**
 * Satis configuration archive options utilities.
 *
 * @author James <james@rezo.net>
 */
class SatisRequireOptions implements Serializable
{
    private $all;

    private $require;

    private $dependencies;

    private $devDependencies;

    private function check()
    {
        if (!$this->all && empty($this->require)) {
            $this->all = true;
        }
    }

    private function cleanArray(array $require)
    {
        //Unset default values
        if (isset($require['require-all']) && false === $require['require-all']) {
            unset($require['require-all']);
        }
        if (isset($require['require']) && is_array($require['require']) && empty($require['require'])) {
            unset($require['require']);
        }
        if (isset($require['require-dependencies']) && false === $require['require-dependencies']) {
            unset($require['require-dependencies']);
        }
        if (isset($require['require-dev-dependencies']) && false === $require['require-dev-dependencies']) {
            unset($require['require-dev-dependencies']);
        }

        //Unset non-allowed keys
        foreach ($require as $key => $value) {
            if (!in_array($key, array(
                'require-all',
                'require',
                'require-dependencies',
                'require-dev-dependencies',
            ))) {
                unset($require[$key]);
            }
        }

        return $require;
    }

    public function __construct()
    {
        $this->all = true;
        $this->require = array();
        $this->dependencies = false;
        $this->devDependencies = false;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->getOptions());
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $require = $this->cleanArray(unserialize($serialized));

        $this->all = isset($require['require-all']) && true === $require['require-all'];
        $this->require = (isset($require['require']) && is_array($require['require'])) ?
            $require['require'] :
            array();
        $this->dependencies = isset($require['require-dependencies']) &&
            true === $require['require-dependencies'];
        $this->devDependencies = isset($require['require-dev-dependencies']) &&
            true === $require['require-dev-dependencies'];
    }

    public function setAll($all = true)
    {
        $this->all = (bool) $all;

        return $this;
    }

    public function getAll()
    {
        return $this->all;
    }

    public function setRequire($requirePackageName, $requirePackageVersion = '*')
    {
        if (is_null($requirePackageVersion)) {
            if (array_key_exists($requirePackageName, $this->require)) {
                unset($this->require[$requirePackageName]);
            }

            return $this;
        }
        $this->require = array_merge($this->require, array($requirePackageName => $requirePackageVersion));

        return $this;
    }

    public function getRequire()
    {
        return $this->require;
    }

    public function setDependencies($dependencies = true)
    {
        $this->dependencies = (bool) $dependencies;

        return $this;
    }

    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function setDevDependencies($devDependencies = true)
    {
        $this->devDependencies = (bool) $devDependencies;

        return $this;
    }

    public function getDevDependencies()
    {
        return $this->devDependencies;
    }

    public function getOptions()
    {
        $this->check();
        $require = array(
            'require-all' => $this->getAll(),
            'require' => $this->getRequire(),
            'require-dependencies' => $this->getDependencies(),
            'require-dev-dependencies' => $this->getDevDependencies(),
        );

        return $this->cleanArray($require);
    }
}
