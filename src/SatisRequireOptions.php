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
    /**
     * Selects all versions of all packages in the defined repositories.
     *
     * @var bool
     */
    private $all;

    /**
     * To cherry pick which packages.
     *
     * @var array
     */
    private $require;

    /**
     * To resolve all the required packages from the listed repositories.
     *
     * @var bool
     */
    private $dependencies;

    /**
     * To resolve all the dev-required packages from the listed repositories.
     *
     * @var bool
     */
    private $devDependencies;

    /**
     * Check require options.
     *
     * @return SatisRequireOptions this SatisRequireOptions Instance
     */
    private function check()
    {
        $this->setAll(empty($this->require));

        return $this;
    }

    /**
     * Unset unknown options or set with default value.
     *
     * @param array $require Set of options
     *
     * @return array Cleaned set of options
     */
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

    /**
     * Contructor.
     */
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
     *
     * @param string $serialized data to set this instance
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
        $this->check();
    }

    /**
     * Sets require-all option.
     *
     * @param bool $all The value to set
     *
     * @return SatisRequireOptions this SatisRequireOptions Instance
     */
    public function setAll($all = true)
    {
        $this->all = (bool) $all;

        return $this;
    }

    /**
     * Gets require-all option..
     *
     * @return bool require-all option
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * Sets require option.
     *
     * @param string $requirePackageName    Package name
     * @param string $requirePackageVersion Package version (default:all)
     *
     * @return SatisRequireOptions this SatisRequireOptions Instance
     */
    public function setRequire($requirePackageName, $requirePackageVersion = '*')
    {
        if (is_null($requirePackageVersion)) {
            if (array_key_exists($requirePackageName, $this->require)) {
                unset($this->require[$requirePackageName]);
            }

            return $this->check();
        }
        $this->require = array_merge($this->require, array($requirePackageName => $requirePackageVersion));

        return $this->check();
    }

    /**
     * Gets require option.
     *
     * @return array require option
     */
    public function getRequire()
    {
        return $this->require;
    }

    /**
     * Sets require-dependencies option.
     *
     * @param bool $dependencies The value to set
     *
     * @return SatisRequireOptions this SatisRequireOptions Instance
     */
    public function setDependencies($dependencies = true)
    {
        $this->dependencies = (bool) $dependencies;

        return $this;
    }

    /**
     * Gets require-dependencies option.
     *
     * @return bool require-dependencies option
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Sets require-dev-dependencies option.
     *
     * @param bool $devDependencies The value to set
     *
     * @return SatisRequireOptions this SatisRequireOptions Instance
     */
    public function setDevDependencies($devDependencies = true)
    {
        $this->devDependencies = (bool) $devDependencies;

        return $this;
    }

    /**
     * Gets require-dev-dependencies option.
     *
     * @return bool require-dev-dependencies option
     */
    public function getDevDependencies()
    {
        return $this->devDependencies;
    }

    /**
     * Gets all the require options.
     *
     * @api
     *
     * @return array Checked and cleaned array
     */
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
