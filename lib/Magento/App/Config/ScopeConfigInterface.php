<?php
/**
 * Configuration interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Config;

interface ScopeConfigInterface
{
    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return mixed
     */
    public function getValue($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null);

    /**
     * Retrieve config flag by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return bool
     */
    public function isSetFlag($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null);
}
