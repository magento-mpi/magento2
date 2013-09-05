<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Config;

interface CacheInterface
{
    /**
     * Retrieve config data
     *
     * @param string $scope
     * @param string $cacheId
     * @return mixed
     */
    public function get($scope, $cacheId);

    /**
     * Save config data to cache
     *
     * @param mixed $data
     * @param string $scope
     * @param string $cacheId
     */
    public function put($data, $scope, $cacheId);
}
