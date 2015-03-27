<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * CRUD implementation for Api Controller.
 *
 * Interface ApiControllerInterface
 *
 * @package ONGR\ApiBundle\Controller
 */
interface ApiControllerInterface
{
    /**
     * Create operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function putData($request, $endpoint);

    /**
     * Read operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function getData($request, $endpoint);

    /**
     * Update operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function postData($request, $endpoint);

    /**
     * Delete operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function deleteData($request, $endpoint);
}
