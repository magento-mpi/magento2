<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\TaxDetails;

use Magento\Tax\Service\V1\Data\TaxDetails;

/**
 * Builder for the TaxDetails Item Data Object
 *
 * @method Item create()
 */

class ItemBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Applied Tax data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder
     */
    protected $appliedTaxBuilder;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param AppliedTaxBuilder $appliedTaxBuilder
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder $appliedTaxBuilder
    ) {
        parent::__construct($objectFactory);
        $this->appliedTaxBuilder = $appliedTaxBuilder;
    }

    /**
     * Set code (sku or shipping code)
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->_set(Item::KEY_CODE, $code);
        return $this;
    }

    /**
     * Set type (shipping, product, weee, gift wrapping, etc.)
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->_set(Item::KEY_TYPE, $type);
        return $this;
    }

    /**
     * Set tax percent
     *
     * @param float $taxPercent
     * @return $this
     */
    public function setTaxPercent($taxPercent)
    {
        $this->_set(Item::KEY_TAX_PERCENT, $taxPercent);
        return $this;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->_set(Item::KEY_PRICE, $price);
        return $this;
    }

    /**
     * Set price including tax
     *
     * @param float $priceInclTax
     * @return $this
     */
    public function setPriceInclTax($priceInclTax)
    {
        $this->_set(Item::KEY_PRICE_INCL_TAX, $priceInclTax);
        return $this;
    }

    /**
     * Set row total
     *
     * @param float $rowTotal
     * @return $this
     */
    public function setRowTotal($rowTotal)
    {
        $this->_set(Item::KEY_ROW_TOTAL, $rowTotal);
        return $this;
    }

    /**
     * Set row total including tax
     *
     * @param float $rowTotalInclTax
     * @return $this
     */
    public function setRowTotalInclTax($rowTotalInclTax)
    {
        $this->_set(Item::KEY_ROW_TOTAL_INCL_TAX, $rowTotalInclTax);
        return $this;
    }

    /**
     * Set tax amount
     *
     * @param float $taxAmount
     * @return $this
     */
    public function setTaxAmount($taxAmount)
    {
        $this->_set(Item::KEY_TAX_AMOUNT, $taxAmount);
        return $this;
    }

    /**
     * Set taxable amount
     *
     * @param float $taxableAmount
     * @return $this
     */
    public function setTaxableAmount($taxableAmount)
    {
        $this->_set(Item::KEY_TAXABLE_AMOUNT, $taxableAmount);
        return $this;
    }

    /**
     * Set discount amount
     *
     * @param float $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->_set(Item::KEY_DISCOUNT_AMOUNT, $discountAmount);
        return $this;
    }

    /**
     * Set discount tax compensation amount
     *
     * @param float $discountTaxCompensationAmount
     * @return $this
     */
    public function setDiscountTaxCompensationAmount($discountTaxCompensationAmount)
    {
        $this->_set(Item::KEY_DISCOUNT_TAX_COMPENSATION_AMOUNT, $discountTaxCompensationAmount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        $appliedTaxDataObjects = [];

        if (isset($data[Item::KEY_APPLIED_TAXES])) {
            $appliedTaxes = $data[Item::KEY_APPLIED_TAXES];
            foreach ($appliedTaxes as $appliedTax) {
                $appliedTaxDataObjects[] = $this->appliedTaxBuilder->populateWithArray($appliedTax)->create();
            }
        }

        $data[Item::KEY_APPLIED_TAXES] = $appliedTaxDataObjects;

        return parent::_setDataValues($data);
    }
}
