<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Url;

class ScopeResolver implements \Magento\Framework\Url\ScopeResolverInterface
{
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var null|string
     */
    protected $_areaCode;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param string|null $areaCode
     */
    public function __construct(\Magento\Framework\StoreManagerInterface $storeManager, $areaCode = null)
    {
        $this->_storeManager = $storeManager;
        $this->_areaCode = $areaCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope($scopeId = null)
    {
        $scope = $this->_storeManager->getStore($scopeId);
        if (!$scope instanceof \Magento\Framework\Url\ScopeInterface) {
            throw new \Magento\Framework\Exception('Invalid scope object');
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
