<?php
/**
 * Application configuration object. Used to access configuration when application is initialized and installed.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class Config implements \Magento\App\ConfigInterface
{
    /**
     * Config cache tag
     */
    const CACHE_TAG = 'CONFIG';

    /**
     * @var \Magento\App\Config\ScopePool
     */
    protected $_scopePool;

    /**
     * @param \Magento\App\Config\ScopePool $scopePool
     */
    public function __construct(\Magento\App\Config\ScopePool $scopePool)
    {
        $this->_scopePool = $scopePool;
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param string $scope
     * @param string $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return $this->_scopePool->getScope($scope, $scopeCode)->getValue($path);
    }

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     * @param string $scope
     * @param null|string $scopeCode
     * @return void
     */
    public function setValue($path, $value, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        $this->_scopePool->getScope($scope, $scopeCode)->setValue($path, $value);
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return bool
     */
    public function isSetFlag($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return (bool)$this->getValue($path, $scope, $scopeCode);
    }
}
