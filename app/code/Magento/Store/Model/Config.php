<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model;

class Config implements \Magento\App\Config\ScopeConfigInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\Resource\Store\Collection
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Store\Model\Resource\Store\CollectionFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        return $this->_storeManager->getStore($scopeCode)->getConfig($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isSetFlag($path, $scope = \Magento\BaseScopeInterface::SCOPE_DEFAULT, $scopeCode = null)
    {
        $flag = strtolower($this->getValue($path, $scope, $scopeCode));
        return !empty($flag) && 'false' !== $flag;
    }
}
