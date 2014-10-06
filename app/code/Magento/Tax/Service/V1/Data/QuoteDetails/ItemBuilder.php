<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\QuoteDetails;

use Magento\Framework\Service\Data\AttributeValueBuilder;
use Magento\Framework\Service\Data\MetadataServiceInterface;

/**
 * Builder for the Item Service Data Object
 *
 * @method Item create()
 */
class ItemBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * TaxClassKey data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder
     */
    protected $taxClassKeyBuilder;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder $taxClassKeyBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->taxClassKeyBuilder = $taxClassKeyBuilder;
    }

    /**
     * Get tax class key builder
     *
     * @return \Magento\Tax\Service\V1\Data\TaxClassKeyBuilder
     */
    public function getTaxClassKeyBuilder()
    {
        return $this->taxClassKeyBuilder;
    }

    /**
     * Set code (sku or shipping code)
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        return $this->_set(Item::KEY_CODE, $code);
    }

    /**
     * Set type (e.g., shipping, product, wee, gift wrapping, etc.)
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->_set(Item::KEY_TYPE, $type);
    }

    /**
     * Set tax class key
     *
     * @param \Magento\Tax\Service\V1\Data\TaxClassKey $taxClassKey
     * @return $this
     */
    public function setTaxClassKey($taxClassKey)
    {
        return $this->_set(Item::KEY_TAX_CLASS_KEY, $taxClassKey);
    }

    /**
     * Set unit price
     *
     * @param float $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice)
    {
        return $this->_set(Item::KEY_UNIT_PRICE, $unitPrice);
    }

    /**
     * Set quantity
     *
     * @param float $quantity
     * @return $this
     */
    public function setQuantity($quantity)
    {
        return $this->_set(Item::KEY_QUANTITY, $quantity);
    }

    /**
     * Set indicate that if the tax is included in the unit price and row total
     *
     * @param bool $taxIncluded
     * @return $this
     */
    public function setTaxIncluded($taxIncluded)
    {
        return $this->_set(Item::KEY_TAX_INCLUDED, $taxIncluded);
    }

    /**
     * Set short description
     *
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription($shortDescription)
    {
        return $this->_set(Item::KEY_SHORT_DESCRIPTION, $shortDescription);
    }

    /**
     * Set discount amount
     *
     * @param float $amount
     * @return $this
     */
    public function setDiscountAmount($amount)
    {
        return $this->_set(Item::KEY_DISCOUNT_AMOUNT, $amount);
    }

    /**
     * Set parent code
     *
     * @param string $code
     * @return $this
     */
    public function setParentCode($code)
    {
        return $this->_set(Item::KEY_PARENT_CODE, $code);
    }

    /**
     * Set associated item code
     *
     * @param string $code
     * @return $this
     */
    public function setAssociatedItemCode($code)
    {
        return $this->_set(Item::KEY_ASSOCIATED_ITEM_CODE, $code);
    }

    /**
     * Set tax class id
     *
     * @param string $code
     * @return $this
     */
    public function setTaxClassId($code)
    {
        return $this->_set(Item::KEY_TAX_CLASS_ID, $code);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(Item::KEY_TAX_CLASS_KEY, $data)) {
            $data[Item::KEY_TAX_CLASS_KEY] = $this->taxClassKeyBuilder->populateWithArray(
                $data[Item::KEY_TAX_CLASS_KEY]
            )->create();
        }

        return parent::_setDataValues($data);
    }
}
