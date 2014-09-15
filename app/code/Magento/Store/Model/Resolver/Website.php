<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model\Resolver;

class Website implements \Magento\Framework\App\ScopeResolverInterface
{
    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\App\InitException
     */
    public function getScope($scopeId = null)
    {
        $scope = $this->_storeManager->getWebsite($scopeId);
        if (!($scope instanceof \Magento\Framework\App\ScopeInterface)) {
            throw new \Magento\Framework\App\InitException('Invalid scope object');
        }

        return $scope;
    }
}
