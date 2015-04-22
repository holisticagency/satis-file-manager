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
class SatisArchiveOptions implements Serializable
{
    /**
     * Create downloads for branches if true.
     *
     * @var bool
     */
    private $enable;

    /**
     * List of required options.
     *
     * @var array
     */
    private $requiredKeys = array('directory');

    /**
     * List of optional options.
     *
     * @var array
     */
    private $optionalKeys = array(
        'format', 'prefix-url', 'skip-dev',
        'whitelist', 'blacklist', 'absolute-directory'
    );

    /**
     * Default values for archive configuration options.
     *
     * @var array
     */
    private $defaultValues = array(
        'format'    => 'zip',
        'skip-dev'  => false,
        'whitelist' => array(),
        'blacklist' => array(),
    );

    /**
     * List of file formats allowed to upload on a Satis Http Server.
     *
     * @var array
     */
    private $allowed = array('format' => array('zip', 'tar'));

    /**
     * Satis archive configuration options
     *
     * @var array
     */
    private $archive;

    /**
     * Checks archive options.
     *
     * @return bool true if all archive options are correct
     */
    private function check()
    {
        //required
        foreach ($this->requiredKeys as $requiredKey) {
            if(!array_key_exists($requiredKey, $this->archive)) {
                return false;
            }
        }

        //allowed
        foreach($this->archive as $key => $value) {
            if(array_key_exists($key, $this->allowed) && !in_array($value, $this->allowed[$key])) {
                return false;
            }
        }

        //unknow keys
        foreach ($this->archive as $key => $value) {
            if(!in_array($key, array_merge($this->requiredKeys, $this->optionalKeys))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Unset options set with default value.
     *
     * @return SatisArchiveOptions this SatisArchiveOptions Instance
     */
    private function clean()
    {
        foreach ($this->archive as $key => $value) {
            if (
                isset($this->defaultValues[$key]) &&
                $this->archive[$key] === $this->defaultValues[$key]
            ) {
                unset($this->archive[$key]);
            }
        }

        return $this;
    }

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->disable();
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize($this->archive);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        $this->archive = unserialize($serialized);
        if (!$this->enable = $this->check()) {
            $this->disable();
        }
    }

    /**
     * Gets archive options.
     *
     * @return array
     */
    public function get()
    {
        if ($this->enable) {
            $this->clean();
            return array('archive' => $this->archive);
        }

        return array();
    }

    /**
     * Sets archive options.
     *
     * @param array $archive Array of archive options.
     *
     * @return SatisArchiveOptions this SatisArchiveOptions Instance
     */
    public function set(array $archive)
    {
        $this->archive = $archive;
        if (!$this->enable = $this->check()) {
            $this->disable();
        }

        return $this;
    }

    /**
     * Enable archive options.
     *
     * @param string $directory relative path for dist archive
     *
     * @return SatisArchiveOptions this SatisArchiveOptions Instance
     */
    public function enable($directory = '')
    {
        $this->enable = true;
        $this->archive = array(
            'directory' => $directory,
        );

        return $this;
    }

    /**
     * Disable archive options.
     *
     * @return SatisArchiveOptions this SatisArchiveOptions Instance
     */
    public function disable()
    {
        $this->enable = false;
        $this->archive = array();

        return $this;
    }
}
