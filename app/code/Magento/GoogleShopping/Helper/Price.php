<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\GoogleShopping\Helper;

use Magento\Catalog\Model\Product as CatalogModelProduct;
use Magento\Core\Model\Store;

/**
 * Price helper
 * This class is workaround for problem of getting appropriate price for
 * some types of products: bundle, grouped, gift cards; abstract price model
 * doesn't give access to such information
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Price
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
    }

    /**
     * Tries to return price that looks like price in catalog
     *
     * @param CatalogModelProduct $product
     * @param null|Store $store Store view
     * @param bool $inclTax
     * @return null|float Price
     */
    public function getCatalogPrice(CatalogModelProduct $product, $store = null, $inclTax = null)
    {
        switch ($product->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                // Workaround to avoid loading stock status by admin's website
                if ($store instanceof Store) {
                    $oldStore = $this->_storeManager->getStore();
                    $this->_storeManager->setCurrentStore($store);
                }
                $subProducts = $product->getTypeInstance()->getAssociatedProducts($product);
                if ($store instanceof Store) {
                    $this->_storeManager->setCurrentStore($oldStore);
                }
                if (!count($subProducts)) {
                    return null;
                }
                $minPrice = null;
                foreach ($subProducts as $subProduct) {
                    $subProduct->setWebsiteId($product->getWebsiteId())
                        ->setCustomerGroupId($product->getCustomerGroupId());
                    if ($subProduct->isSalable()) {
                        if ($this->getCatalogPrice($subProduct) < $minPrice || $minPrice === null) {
                            $minPrice = $this->getCatalogPrice($subProduct);
                            $product->setTaxClassId($subProduct->getTaxClassId());
                        }
                    }
                }
                return $minPrice;

            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                if ($store instanceof Store) {
                    $oldStore = $this->_storeManager->getStore();
                    $this->_storeManager->setCurrentStore($store);
                }

                $this->_coreRegistry->unregister('rule_data');
                $this->_coreRegistry->register('rule_data', new \Magento\Object(array(
                    'store_id'          => $product->getStoreId(),
                    'website_id'        => $product->getWebsiteId(),
                    'customer_group_id' => $product->getCustomerGroupId())));

                $minPrice = $product->getPriceModel()->getTotalPrices($product, 'min', $inclTax);

                if ($store instanceof Store) {
                    $this->_storeManager->setCurrentStore($oldStore);
                }
                return $minPrice;

            case 'giftcard':
                return $product->getPriceModel()->getMinAmount($product);

            default:
                return $product->getFinalPrice();
        }
    }

    /**
     * Tries calculate price without discount; if can't returns nul
     *
     * @param CatalogModelProduct $product
     * @param null|Store $store
     * @return float|null
     */
    public function getCatalogRegularPrice(CatalogModelProduct $product, $store = null)
    {
        switch ($product->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
            case 'giftcard':
                return null;

            default:
                return $product->getPrice();
        }
    }
}
