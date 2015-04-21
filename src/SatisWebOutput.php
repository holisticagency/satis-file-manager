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

/**
 * Satis configuration web outputs utilities.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisWebOutput
{
    /**
     * controls whether the repository has an html page as well or not.
     *
     * @var bool
     */
    private $outputHtml;

    /**
     * Path to a twig template directory.
     *
     * @var string
     */
    private $twigTemplate;

    /**
     * List of file extensions allowed to upload on a Satis Http Server.
     *
     * @var array
     */
    private $allowedFiles = array('html', 'css', 'js');

    /**
     * Constructor.
     *
     * @param string $twigTemplate path to a twig template directory
     */
    public function __construct($twigTemplate = null)
    {
        $this->set($twigTemplate);
    }

    /**
     * Sets html output.
     *
     * @param string $twigTemplate path to a twig template directory
     *
     * @return SatisWebOutput this SatisWebOutput Instance
     */
    public function set($twigTemplate = null)
    {
        $this->outputHtml = true;
        $this->twigTemplate = $twigTemplate;

        return $this;
    }

    /**
     * Disable html output.
     *
     * @return SatisWebOutput this SatisWebOutput Instance
     */
    public function disable()
    {
        $this->outputHtml = false;
        $this->twigTemplate = null;

        return $this;
    }

    /**
     * Gets html output.
     *
     * @return array
     */
    public function get()
    {
        $webConfig = array();

        if ($this->outputHtml == false) {
            $webConfig['output-html'] = false;
        }
        if (is_string($this->twigTemplate)) {
            $webConfig['twig-template'] = $this->twigTemplate;
        }

        return $webConfig;
    }

    /**
     * Sets file extensions allowed to upload on a Satis Http Server.
     *
     * @param array $allowed file extensions allowed to upload
     *
     * @return SatisWebOutput this SatisWebOutput Instance
     */
    public function setAllowedExtensions(array $allowed)
    {
        $this->allowedFiles = $allowed;

        return $this;
    }

    /**
     * Gets file extensions allowed to upload on a Satis Http Server.
     *
     * @return array file extensions allowed to upload
     */
    public function getAllowedExtensions()
    {
        return $this->allowedFiles;
    }
}
