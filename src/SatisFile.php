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

use Composer\Json\JsonFile;
use Composer\Repository\RepositoryInterface;
use Composer\Package\PackageInterface;

/**
 * Satis configuration file utilities.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisFile
{
    /**
     * An arbitrary name.
     *
     * @var string
     */
    private $name = 'default name';

    /**
     * Homepage of the static Composer repository.
     *
     * @var string
     */
    private $homepage;

    /**
     * Indexed Repositories by md5 hash.
     *
     * @var array
     */
    private $repositories = array();

    private $requireOptions;

    /**
     * Configuration options for html output.
     *
     * @var SatisWebOutput
     */
    private $webOptions;

    /**
     * Configuration options for dist archive.
     *
     * @var SatisArchiveOptions
     */
    private $archiveOptions;

    /**
     * Configuration array.
     *
     * @var array
     */
    private $satisConfig;

    /**
     * Constructor.
     *
     * @param string       $homepage       Homepage
     * @param array|string $existingConfig Array|json string of an existing configuration
     */
    public function __construct($homepage, $existingConfig = null)
    {
        $this->homepage = rtrim($homepage, '/');
        $this->requireOptions = new SatisRequireOptions();
        $this->webOptions = new SatisWebOutput();
        $this->archiveOptions = new SatisArchiveOptions();

        if (is_string($existingConfig) && $tmpConfig = JsonFile::parseJson($existingConfig)) {
            $existingConfig = $tmpConfig;
        }

        if (isset($existingConfig['archive']) && is_array($existingConfig['archive'])) {
            $this->archiveOptions->set($existingConfig['archive']);
        }

        if (isset($existingConfig['twig-template'])) {
            $this->webOptions->set($existingConfig['twig-template']);
        } elseif (isset($existingConfig['output-html'])) {
            if (false === $existingConfig['output-html']) {
                $this->webOptions->disable();
            }
        }

        $this->satisConfig = $existingConfig ?: $this->getDefaultConfig();

        foreach ($this->satisConfig['repositories'] as $index => $satisRepository) {
            $this->repositories[$index] = md5(implode($satisRepository));
        }
    }

    /**
     * Gives a default Configuration when creating a repository.
     *
     * @return array default Configuration
     */
    private function getDefaultConfig()
    {
        $defaults = array(
            'name'  => $this->name,
            'homepage' => $this->homepage,
            'repositories' => array(),
        );

        $requireOptions = $this->requireOptions->getOptions();
        $defaults = array_merge($defaults, $requireOptions);

        $webOptions = $this->webOptions->disable()->get();
        $defaults = array_merge($defaults, $webOptions);

        $this->archiveOptions->set(array('directory' => 'dist'));
        $defaults = array_merge($defaults, $this->archiveOptions->get());

        return $defaults;
    }

    /**
     * Check the type of a repository.
     *
     * @param RepositoryInterface $repository a repository to set or unset in the configuration
     *
     * @throws \Exception If $repository is of an unsupported class
     *
     * @return array Combination of a type and an url
     */
    private function setRepositoryHelper(RepositoryInterface $repository)
    {
        $satisRepository = array();
        if ($repository instanceof \Composer\Repository\ComposerRepository) {
            $repository = new SatisComposerRepository($repository);
            $satisRepository = array('type' => 'composer', 'url' => $repository->getUrl());
        } elseif ($repository instanceof \Composer\Repository\VcsRepository) {
            $repo = $repository->getRepoConfig();
            $satisRepository = array('type' => $repo['type'], 'url' => $repo['url']);
        } elseif ($repository instanceof \Composer\Repository\ArtifactRepository) {
            $repository = new SatisArtifactRepository($repository);
            $satisRepository = array('type' => 'artifact', 'url' => $repository->getLookup());
        } else {
            throw new \Exception('Error Processing Request', 1);
        }

        return $satisRepository;
    }

    /**
     * Set a Repository in the configuration.
     *
     * @param RepositoryInterface $repository The repository to set
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $satisRepository = $this->setRepositoryHelper($repository);
        //Check url
        $changeType = false;
        foreach ($this->satisConfig['repositories'] as $index => $configRepository) {
            if ($satisRepository['url'] === $configRepository['url']) {
                //Change type of repository
                $changeType = $index;
            }
        }
        $hashedSatisRepo = md5(implode($satisRepository));
        if (is_numeric($changeType)) {
            //Change type of repository
            $this->satisConfig['repositories'][$index]['type'] = $satisRepository['type'];
            $this->repositories[$index] = $hashedSatisRepo;
        } elseif (!in_array($hashedSatisRepo, $this->repositories)) {
            //Add a repository
            $index = count($this->satisConfig['repositories']);
            $this->satisConfig['repositories'][] = $satisRepository;
            $this->repositories[$index] = $hashedSatisRepo;
        }

        return $this;
    }

    public function getRequireOptions()
    {
        return $this->requireOptions->getOptions();
    }

    /**
     * Sets a Package to cherry pick.
     *
     * @param PackageInterface $package The package to cherry pick
     * @param string           $version Constraint version
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setPackage(PackageInterface $package, $version = '*')
    {
        $this->requireOptions->setRequire($package->getName(), $version);

        return $this;
    }

    public function unsetPackage(PackageInterface $package)
    {
        $this->requireOptions->setRequire($package->getName(), null);

        return $this;
    }

    public function enableRequireDependencies()
    {
        $this->requireOptions->setDependencies();

        return $this;
    }

    public function disableRequireDependencies()
    {
        $this->requireOptions->setDependencies(false);

        return $this;
    }

    public function enableRequireDevDependencies()
    {
        $this->requireOptions->setDevDependencies();

        return $this;
    }

    public function disableRequireDevDependencies()
    {
        $this->requireOptions->setDevDependencies(false);

        return $this;
    }

    /**
     * Sets the minimum stability of the packages to build in the repository.
     *
     * @param string $minimumStability the new minimum stability of the repository
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setStability($minimumStability = '')
    {
        //unset or default value
        if (in_array($minimumStability, array(null, '', 'dev'))) {
            unset($this->satisConfig['minimum-stability']);
        }

        //alowed values
        if (in_array($minimumStability, array('stable', 'RC', 'beta', 'alpha'))) {
            $this->satisConfig['minimum-stability'] = $minimumStability;
        }

        return $this;
    }

    /**
     * Sets the name of the repository.
     *
     * @param string $name the new name of the repository
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setName($name = 'default name')
    {
        $this->name = $name;
        $this->satisConfig['name'] = $name;

        return $this;
    }

    /**
     * Gets configuration options for html output.
     *
     * @return SatisWebOutput configuration options for html output
     */
    public function getWebOptions()
    {
        return $this->webOptions;
    }

    /**
     * Sets configuration options for html output.
     *
     * @param array $webOptions Options to set
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setWebOptions(array $webOptions)
    {
        if (isset($webOptions['output-html']) && $webOptions['output-html'] == false) {
            $this->webOptions->disable();
        }

        if (isset($webOptions['twig-template']) && is_string($webOptions['twig-template'])) {
            $this->webOptions->set($webOptions['twig-template']);
        }

        return $this;
    }

    /**
     * Gets configuration options for dist downloads.
     *
     * @return array Configuration options for dist downloads
     */
    public function getArchiveOptions()
    {
        $archiveOptions = $this->archiveOptions->get();

        return isset($archiveOptions['archive']) ? $archiveOptions['archive'] : array();
    }

    /**
     * Sets configuration options for dist downloads.
     *
     * @param array $archiveOptions Options to set
     *
     * @return SatisFile this SatisFile Instance
     */
    public function setArchiveOptions(array $archiveOptions)
    {
        $this->archiveOptions->set($archiveOptions);

        return $this;
    }

    /**
     * Disable configuration options for dist downloads.
     *
     * @return SatisFile this SatisFile Instance
     */
    public function disableArchiveOptions()
    {
        $this->archiveOptions->disable();

        return $this;
    }

    /**
     * Unset a Repository out of the configuration.
     *
     * @param RepositoryInterface $repository The repository to set
     *
     * @return SatisFile this SatisFile Instance
     */
    public function unsetRepository(RepositoryInterface $repository)
    {
        $satisRepository = $this->setRepositoryHelper($repository);
        $hashedSatisRepo = md5(implode($satisRepository));
        $index = array_search($hashedSatisRepo, $this->repositories);
        if (is_numeric($index)) {
            //Remove a repository
            array_splice($this->satisConfig['repositories'], $index, 1);
            array_splice($this->repositories, $index, 1);
        }

        return $this;
    }

    /**
     * Merge all options config.
     *
     * @return array the Satis configuration as an array
     */
    private function doConfig()
    {
        return array_merge(
            $this->satisConfig,
            $this->getRequireOptions(),
            $this->getWebOptions()->get(),
            array('archive' => $this->getArchiveOptions())
        );
    }

    /**
     * Configuration as an array.
     *
     * @return array Configuration
     */
    public function asArray()
    {
        return $this->doConfig();
    }

    /**
     * Configuration as a Json string.
     *
     * @return string Configuration
     */
    public function json()
    {
        return JsonFile::encode($this->doConfig());
    }
}
