<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

/**
 * ProductType attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ProductType extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_categoryFactory = $categoryFactory;
        parent::__construct(
            $context,
            $registry,
            $productFactory,
            $gsData,
            $gsProduct,
            $catalogPrice,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set current attribute to entry (for specified product)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Gdata\Gshopping\Entry $entry
     * @return \Magento\Framework\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {

        $productCategories = $product->getCategoryIds();

        // TODO: set Default value for product_type attribute if product isn't assigned for any category
        $value = 'Shop';

        if (!empty($productCategories)) {
            $category = $this->_categoryFactory->create()->load(array_shift($productCategories));

            $breadcrumbs = array();

            foreach ($category->getParentCategories() as $cat) {
                $breadcrumbs[] = $cat->getName();
            }

            $value = implode(' > ', $breadcrumbs);
        }

        $this->_setAttribute($entry, 'product_type', self::ATTRIBUTE_TYPE_TEXT, $value);
        return $entry;
    }
}
