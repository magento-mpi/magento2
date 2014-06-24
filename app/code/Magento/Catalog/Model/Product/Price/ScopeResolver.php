<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Price;

class ScopeResolver
{
    /**
     * Price scope
     *
     * @var int
     */
    protected $scope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->storeManager = $storeManager;
        $this->scope = (int)$config->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get current price scope
     *
     * @return int
     */
    public function getScope()
    {
        return $this->scope == \Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL
            ? \Magento\Store\Model\Store::DEFAULT_STORE_ID
            : $this->storeManager->getStore()->getId();
    }
}
