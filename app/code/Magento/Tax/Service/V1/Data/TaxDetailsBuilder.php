<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * Builder for the TaxDetails Data Object
 *
 * @method TaxDetails create()
 */
class TaxDetailsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Applied Tax data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder
     */
    protected $appliedTaxBuilder;

    /**
     * Tax Details Item data object builder
     *
     * @var \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder
     */
    protected $taxDetailsItemBuilder;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\Service\Data\ObjectFactory $objectFactory
     * @param TaxDetails\AppliedTaxBuilder $appliedTaxBuilder
     * @param TaxDetails\ItemBuilder $taxDetailsItemBuilder
     */
    public function __construct(
        \Magento\Framework\Service\Data\ObjectFactory $objectFactory,
        \Magento\Tax\Service\V1\Data\TaxDetails\AppliedTaxBuilder $appliedTaxBuilder,
        \Magento\Tax\Service\V1\Data\TaxDetails\ItemBuilder $taxDetailsItemBuilder
    ) {
        parent::__construct($objectFactory);
        $this->appliedTaxBuilder = $appliedTaxBuilder;
        $this->taxDetailsItemBuilder = $taxDetailsItemBuilder;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal)
    {
        $this->_set(TaxDetails::KEY_SUBTOTAL, $subtotal);
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
        $this->_set(TaxDetails::KEY_TAX_AMOUNT, $taxAmount);
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
        $this->_set(TaxDetails::KEY_TAXABLE_AMOUNT, $taxableAmount);
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
        $this->_set(TaxDetails::KEY_DISCOUNT_AMOUNT, $discountAmount);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function _setDataValues(array $data)
    {
        $appliedTaxDataObjects = [];
        $taxDetailItemDataObjects = [];

        if (isset($data[TaxDetails::KEY_APPLIED_TAXES])) {
            $appliedTaxes = $data[TaxDetails::KEY_APPLIED_TAXES];
            foreach ($appliedTaxes as $appliedTax) {
                $appliedTaxDataObjects[] = $this->appliedTaxBuilder
                    ->populateWithArray($appliedTax)->create();
            }
        }

        if (isset($data[TaxDetails::KEY_ITEMS])) {
            $taxDetailItems = $data[TaxDetails::KEY_ITEMS];
            foreach ($taxDetailItems as $taxDetailItem) {
                $taxDetailItemDataObjects[] = $this->taxDetailsItemBuilder
                    ->populateWithArray($taxDetailItem)->create();
            }
        }

        $data[TaxDetails::KEY_APPLIED_TAXES] = $appliedTaxDataObjects;
        $data[TaxDetails::KEY_ITEMS] = $taxDetailItemDataObjects;

        return parent::_setDataValues($data);
    }
}
