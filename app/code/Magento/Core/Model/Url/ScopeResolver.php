<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Url;


class ScopeResolver implements \Magento\Url\ScopeResolverInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope($scopeId = null)
    {
        $scope = $this->_storeManager->getStore($scopeId);
        if (!($scope instanceof \Magento\Url\ScopeInterface)) {
            throw new \Magento\Exception('Invalid scope object');
        }

        return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getScopes()
    {
        return $this->_storeManager->getStores();
    }
}
