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
 * GoogleProductCategory attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Attribute;

class GoogleProductCategory extends \Magento\GoogleShopping\Model\Attribute\DefaultAttribute
{
    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * Type factory
     *
     * @var \Magento\GoogleShopping\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @param \Magento\GoogleShopping\Model\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\GoogleShopping\Helper\Price $gsPrice
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\GoogleShopping\Model\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Model\Config $config,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\GoogleShopping\Helper\Price $gsPrice,
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_typeFactory = $typeFactory;
        $this->_config = $config;
        parent::__construct($productFactory, $gsData, $gsProduct, $gsPrice, $context, $registry, $resource,
            $resourceCollection, $data);
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
        $targetCountry = $this->_config->getTargetCountry($product->getStoreId());
        $value = $this->_typeFactory->create()->loadByAttributeSetId($product->getAttributeSetId(), $targetCountry);

        $val = ($value->getCategory() == \Magento\GoogleShopping\Helper\Category::CATEGORY_OTHER)
            ? ''
            : $value->getCategory();

        $this->_setAttribute(
            $entry,
            'google_product_category',
            self::ATTRIBUTE_TYPE_TEXT,
            htmlspecialchars_decode($val, ENT_NOQUOTES)
        );
        return $entry;
    }
}
