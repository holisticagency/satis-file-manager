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

use Composer\Repository\RepositoryInterface;
use Composer\Repository\PackageRepository;
use Composer\Package\Package;

/**
 * Satis configuration file utilities.
 *
 * @author James <james@rezo.net>
 */
class SatisPackageRepository extends PackageRepository
{
    protected $package;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository PackageRepository object
     */
    public function __construct(RepositoryInterface $repository)
    {
        $packages = $repository->getPackages();
        $this->package = $packages[0];
    }

    /**
     * Public method to expose the configuration of a PackageRepository needed for.
     *
     * @return array The configuration to be exposed
     */
    public function getSatisConfiguration()
    {
        $config = array(
            'type' => 'package',
            'package' => array(
                'name' => $this->package->getName(),
                'version' => $this->package->getPrettyVersion(),
                'dist' => array(
                    'type' => $this->package->getDistType(),
                    'url' => $this->package->getDistUrl(),
                ),
                'source' => array(
                    'type' => $this->package->getSourceType(),
                    'url' => $this->package->getSourceUrl(),
                ),
            ),
        );

        if (!is_null($this->package->getDistReference())) {
            $config['package']['dist']['reference'] = $this->package->getDistReference();
        }
        if (!is_null($this->package->getSourceReference())) {
            $config['package']['source']['reference'] = $this->package->getSourceReference();
        }
        if (!is_null($this->package->getAutoload())) {
            $config['package']['autoload'] = $this->package->getAutoload();
        }

        return $config;
    }
}
