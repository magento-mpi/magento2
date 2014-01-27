<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Model\Product;

/**
 * Price model for external catalogs
 */
class CatalogPrice implements \Magento\Catalog\Model\Product\CatalogPriceInterface
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\CatalogPrice
     */
    protected $commonPriceModel;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\CatalogPrice $commonPriceModel
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\CatalogPrice $commonPriceModel,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->storeManager = $storeManager;
        $this->commonPriceModel = $commonPriceModel;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Minimal price for "regular" user
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|\Magento\Core\Model\Store $store Store view
     * @param bool $inclTax
     * @return null|float
     */
    public function getCatalogPrice(\Magento\Catalog\Model\Product $product, $store = null, $inclTax = false)
    {
        if ($store instanceof \Magento\Core\Model\Store) {
            $oldStore = $this->storeManager->getStore();
            $this->storeManager->setCurrentStore($store);
        }

        $this->coreRegistry->unregister('rule_data');
        $this->coreRegistry->register('rule_data', new \Magento\Object(array(
            'store_id'          => $product->getStoreId(),
            'website_id'        => $product->getWebsiteId(),
            'customer_group_id' => $product->getCustomerGroupId())));

        $minPrice = $product->getPriceModel()->getTotalPrices($product, 'min', $inclTax);

        if ($store instanceof \Magento\Core\Model\Store) {
            $this->storeManager->setCurrentStore($oldStore);
        }
        return $minPrice;
    }

    /**
     * Regular catalog price not applicable for bundle product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return null
     */
    public function getCatalogRegularPrice(\Magento\Catalog\Model\Product $product)
    {
        return null;
    }
}