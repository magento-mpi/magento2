<?php
/**
 * Application configuration object. Used to access configuration when application is installed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class MutableScopeConfig extends \Magento\App\Config implements \Magento\App\Config\MutableScopeConfigInterface
{
    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     * @return void
     */
    public function setValue($path, $value, $scope = \Magento\App\ScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        $this->_scopePool->getScope($scope, $scopeCode)->setValue($path, $value);
    }
}
