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

class Config implements \Magento\App\Config\ScopeConfigInterface
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
     * @param null|string $scopeCode
     * @return mixed
     */
    public function getValue($path = null, $scope = \Magento\App\ScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return $this->_scopePool->getScope($scope, $scopeCode)->getValue($path);
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @param string $scope
     * @param null|string $scopeCode
     * @return bool
     */
    public function isSetFlag($path, $scope = \Magento\App\ScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return (bool)$this->getValue($path, $scope, $scopeCode);
    }
}
