<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Attribute;

/**
 * GoogleProductCategory attribute model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
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
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\GoogleShopping\Helper\Price $gsPrice
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
     * @param \Magento\GoogleShopping\Model\TypeFactory $typeFactory
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GoogleShopping\Helper\Data $gsData,
        \Magento\GoogleShopping\Helper\Product $gsProduct,
        \Magento\GoogleShopping\Helper\Price $gsPrice,
        \Magento\GoogleShopping\Model\Resource\Attribute $resource,
        \Magento\GoogleShopping\Model\TypeFactory $typeFactory,
        \Magento\GoogleShopping\Model\Config $config,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_typeFactory = $typeFactory;
        $this->_config = $config;
        parent::__construct(
            $context,
            $registry,
            $productFactory,
            $gsData,
            $gsProduct,
            $gsPrice,
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
