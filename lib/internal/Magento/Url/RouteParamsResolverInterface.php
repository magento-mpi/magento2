<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Url;

interface RouteParamsResolverInterface
{
    /**
     * Set route params
     *
     * @param array $data
     * @param boolean $unsetOldParams
     * @return RouteParamsResolverInterface
     */
    public function setRouteParams(array $data, $unsetOldParams = true);

    /**
     * Set route param
     *
     * @param string $key
     * @param mixed $data
     * @return RouteParamsResolverInterface
     */
    public function setRouteParam($key, $data);

    /**
     * Retrieve route params
     *
     * @return array
     */
    public function getRouteParams();

    /**
     * Retrieve route param
     *
     * @param string $key
     * @return mixed
     */
    public function getRouteParam($key);
}
