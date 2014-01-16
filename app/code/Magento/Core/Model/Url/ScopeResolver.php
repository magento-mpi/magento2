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
     * @var null|string
     */
    protected $_areaCode;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param string|null $areaCode
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $areaCode = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_areaCode = $areaCode;
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

    /**
     * {@inheritdoc}
     */
    public function getAreaCode()
    {
        return $this->_areaCode;
    }
}
