<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Routing;

use ONGR\ElasticsearchBundle\ORM\Manager;
use Symfony\Component\Routing\RequestContext;
use Symfony\Bundle\FrameworkBundle\Routing\Router as BaseRouter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Overrides default framework router.
 */
class Router extends BaseRouter
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        ContainerInterface $container,
        $resource,
        array $options = [],
        RequestContext $context = null
    ) {
        $this->container = $container;
        parent::__construct($container, $resource, $options, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getMatcher()
    {
        $parentMatcher = parent::getMatcher();

        $matcher = new ApiMatcher($parentMatcher, $this->container, []);

        return $matcher;
    }
}
