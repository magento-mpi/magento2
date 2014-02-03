<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Image link attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Attribute;

class ImageLink extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * @var \Magento\Catalog\Helper\Product|null
     */
    protected $_catalogProduct = null;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\Catalog\Model\Product\CatalogPrice $catalogPrice,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogProduct = $catalogProduct;
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
     * @param \Magento\Gdata\Gshopping\Entry $entry
     * @return \Magento\Gdata\Gshopping\Entry
     */
    public function convertAttribute($product, $entry)
    {
        $url = $this->_catalogProduct->getImageUrl($product);
        if ($product->getImage() && $product->getImage() != 'no_selection' && $url) {
            $this->_setAttribute($entry, 'image_link', self::ATTRIBUTE_TYPE_URL, $url);
        }
        return $entry;
    }
}
