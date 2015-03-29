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
use Composer\Repository\ComposerRepository;

/**
 * Satis configuration file utilities.
 *
 * @author Gil <gillesodret@users.noreply.github.com>
 */
class SatisComposerRepository extends ComposerRepository
{
    /**
     * The url to be exposed.
     *
     * @var string
     */
    protected $url;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $repository ComposerRepository object
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->url = $repository->url;
    }

    /**
     * Public method to expose the url of a ComposerRepository.
     *
     * @return string The url to be exposed
     */
    public function getUrl()
    {
        return $this->url;
    }
}
