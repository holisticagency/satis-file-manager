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
use Composer\Repository\PathRepository;

/**
 * Satis configuration file utilities.
 *
 * @author James <james@rezo.net>
 */
class SatisPathRepository extends PathRepository
{
    /**
     * The url path to be exposed.
     *
     * @var string
     */
    protected $url;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository PathRepository object
     */
    public function __construct(RepositoryInterface $repository)
    {
        $reflection = new \ReflectionProperty(get_class($repository), 'url');
        $reflection->setAccessible(true);
        $this->url = $reflection->getValue($repository); #$repository->url;
    }

    /**
     * Public method to expose the path of a PathRepository.
     *
     * @return string The url path to be exposed
     */
    public function getUrl()
    {
        return $this->url;
    }
}
