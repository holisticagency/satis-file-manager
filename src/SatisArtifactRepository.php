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
use Composer\Repository\ArtifactRepository;

/**
 * Satis configuration file utilities.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisArtifactRepository extends ArtifactRepository
{
    /**
     * The lookup path to be exposed.
     *
     * @var string
     */
    protected $lookup;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository ArtifactRepository object
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->lookup = $repository->lookup;
    }

    /**
     * Public method to expose the path of an ArtifactRepository.
     *
     * @return string The lookup path to be exposed
     */
    public function getLookup()
    {
        return $this->lookup;
    }
}
