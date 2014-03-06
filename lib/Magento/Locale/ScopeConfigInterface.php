<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

interface ScopeConfigInterface
{
    /**
     * Retrieve scope config value
     *
     * @param string $path
     * @param mixed $scope
     * @return mixed
     */
    public function getConfig($path, $scope = null);

    /**
     * Retrieve scope config flag
     *
     * @param string $path
     * @param mixed $scope
     * @return bool
     */
    public function getConfigFlag($path, $scope = null);
}
