<?php
/**
 * Scope Resolver
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Scope;

class Resolver implements \Magento\App\Config\Scope\ResolverInterface
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
    public function getScopeCode($scopeId = null)
    {
        $scope = $this->_storeManager->getStore($scopeId);
        if (!($scope instanceof \Magento\App\Config\ScopeInterface)) {
            throw new \Magento\Exception('Invalid scope object');
        }

        return $scope->getCode();
    }
}
