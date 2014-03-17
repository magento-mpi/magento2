<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Rule\Condition;

/**
 * Product rule condition data model
 *
 * @category Magento
 * @package Magento_SalesRule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Locale\FormatInterface $localeFormat
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $product,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->_productFactory = $productFactory;
    }

    /**
     * Add special attributes
     *
     * @param array $attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = __('Quantity in cart');
        $attributes['quote_item_price'] = __('Price in cart');
        $attributes['quote_item_row_total'] = __('Row total in cart');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param \Magento\Object $object
     * @return bool
     */
    public function validate(\Magento\Object $object)
    {
        //@todo reimplement this method when is fixed MAGETWO-5713
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $object->getProduct();
        if (!$product instanceof \Magento\Catalog\Model\Product) {
            $product = $this->_productFactory->create()->load($object->getProductId());
        }

        $product->setQuoteItemQty(
            $object->getQty()
        )->setQuoteItemPrice(
            $object->getPrice()
        )->setQuoteItemRowTotal(
            $object->getBaseRowTotal()
        );

        return parent::validate($product);
    }
}
