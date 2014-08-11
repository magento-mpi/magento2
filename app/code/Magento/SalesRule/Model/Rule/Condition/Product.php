<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Model\Rule\Condition;

/**
 * Product rule condition data model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Resource\Product $productResource
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Resource\Product $productResource,
        \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
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
     * @param \Magento\Framework\Object $object
     * @return bool
     */
    public function validate(\Magento\Framework\Object $object)
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
            $object->getPrice() // possible bug: need to use $object->getBasePrice()
        )->setQuoteItemRowTotal(
            $object->getBaseRowTotal()
        );

        return parent::validate($product);
    }
}
