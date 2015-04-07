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

use ONGR\ElasticsearchBundle\DSL\Query\MatchQuery;
use ONGR\ElasticsearchBundle\DSL\Query\TermQuery;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\DemoBundle\Document\parents\Traits\SeoAwareTrait;
use Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * URL matcher with extended functionality for document matching.
 */
class ApiMatcher extends RedirectableUrlMatcher
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RedirectableUrlMatcher
     */
    protected $cachedMatcher = null;

    /**
     * @var array
     *
     * Request types supported by API.
     */
    public static $supportedTypes = [
        'PUT',
        'GET',
        'POST',
        'DELETE',
    ];

    /**
     * @param RedirectableUrlMatcher $parentMatcher Parent matcher that is called when this matcher fails.
     * @param ContainerInterface     $container     Container.
     * @param array                  $typeMap       Type map.
     * @param bool                   $allowHttps    Is https allowed.
     */
    public function __construct($parentMatcher, ContainerInterface $container, $typeMap, $allowHttps = false)
    {
        $this->container = $container;
        $this->cachedMatcher = $parentMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        // Try to load default sf route first.
        try {
            return $this->getCachedMatcher()->match($pathinfo);
        } catch (ResourceNotFoundException $e) {
            $prefix = trim($this->container->getParameter('ongr_api.api_prefix'), '/') . '/';
            $pathinfo = trim($pathinfo, '/');

            if (substr($pathinfo, 0, strlen($prefix)) !== $prefix) {
                throw $e;
            }
            $pathinfo = substr($pathinfo, strlen($prefix));

            $endpointInfo = explode('/', $pathinfo);

            $endpointInfoSize = count($endpointInfo);
            if (($endpointInfoSize < 2) && ($endpointInfoSize > 3)) {
                throw $e;
            }
            try {
                $controller = $this->container->getParameter("ongr_api.$endpointInfo[0].$endpointInfo[1].controller");
            } catch (InvalidArgumentException $exc) {
                throw $e;
            }

            $method = $this->cachedMatcher->getContext()->getMethod();
            if (!in_array($method, self::$supportedTypes)) {
                throw $e;
            }
            $params = [];
            if ($controller != 'default') {
                $params['_controller'] = $controller['controller'] . ':' . $method;
                $params['endpoint'] = $this->container->getParameter("ongr_api.$endpointInfo[0].$endpointInfo[1]");
            } else {
                $params['_controller'] = "ONGRApiBundle:Api:$method";
                $params['endpoint'] = "ongr_api.service.$endpointInfo[0].$endpointInfo[1].data_request";
            }
            $params['_route'] = "ongr_api_$endpointInfo[0]_$endpointInfo[1]_$method";
            $params['id'] = isset($endpointInfo[2]) ? $endpointInfo[2] : null;

            return $params;
        }
    }

    /**
     * @return RedirectableUrlMatcher
     */
    public function getCachedMatcher()
    {
        return $this->cachedMatcher;
    }
}
