<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model;

/**
 * Attributes Model
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Attribute extends \Magento\Core\Model\AbstractModel
{
    /**
     * Default ignored attribute codes
     *
     * @var string[]
     */
    protected $_ignoredAttributeCodes = array(
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'custom_layout_update',
        'gift_message_available',
        'giftcard_amounts',
        'news_from_date',
        'news_to_date',
        'options_container',
        'price_view',
        'sku_type',
        'use_config_is_redeemable',
        'use_config_allow_message',
        'use_config_lifetime',
        'use_config_email_template',
        'tier_price',
        'minimal_price',
        'recurring_profile',
        'shipment_type'
    );

    /**
     * Default ignored attribute types
     *
     * @var string[]
     */
    protected $_ignoredAttributeTypes = array('hidden', 'media_image', 'image', 'gallery');

    /**
     * @var \Magento\GoogleShopping\Helper\Data|null
     */
    protected $_gsData = null;

    /**
     * @var \Magento\GoogleShopping\Helper\Product|null
     */
    protected $_gsProduct = null;

    /**
     * @var \Magento\GoogleShopping\Helper\Price|null
     */
    protected $_gsPrice = null;

    /**
     * Product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GoogleShopping\Helper\Data $gsData
     * @param \Magento\GoogleShopping\Helper\Product $gsProduct
     * @param \Magento\GoogleShopping\Helper\Price $gsPrice
     * @param \Magento\GoogleShopping\Model\Resource\Attribute $resource
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
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_gsData = $gsData;
        $this->_gsProduct = $gsProduct;
        $this->_gsPrice = $gsPrice;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magento\GoogleShopping\Model\Resource\Attribute');
    }

    /**
     * Get array with allowed product attributes (for mapping) by selected attribute set
     *
     * @param int $setId attribute set id
     * @return array
     */
    public function getAllowedAttributes($setId)
    {
        $attributes = $this->_productFactory->create()->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $titles = array();
        foreach ($attributes as $attribute) {
            /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            if ($attribute->isInSet($setId) && $this->_isAllowedAttribute($attribute)) {
                $list[$attribute->getAttributeId()] = $attribute;
                $titles[$attribute->getAttributeId()] = $attribute->getFrontendLabel();
            }
        }
        asort($titles);
        $result = array();
        foreach ($titles as $attributeId => $label) {
            $result[$attributeId] = $list[$attributeId];
        }
        return $result;
    }

    /**
     * Check if attribute allowed
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return bool
     */
    protected function _isAllowedAttribute($attribute)
    {
        return !in_array($attribute->getFrontendInput(), $this->_ignoredAttributeTypes)
               && !in_array($attribute->getAttributeCode(), $this->_ignoredAttributeCodes)
               && $attribute->getFrontendLabel() != "";
    }
}
