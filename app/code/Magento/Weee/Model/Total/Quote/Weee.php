<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Total\Quote;

use Magento\Store\Model\Store;

class Weee extends \Magento\Tax\Model\Sales\Total\Quote\Tax
{
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Flag which notify what tax amount can be affected by fixed product tax
     *
     * @var bool
     */
    protected $_isTaxAffected;

    /**
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Tax\Model\Calculation $calculation
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Weee\Helper\Data $weeeHelper
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Weee\Helper\Data $weeeHelper
    ) {
        parent::__construct($taxData, $calculation, $taxConfig, $weeeHelper);
        $this->setCode('weee');
    }

    /**
     * Collect Weee taxes amount and prepare items prices for taxation and discount
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        \Magento\Sales\Model\Quote\Address\Total\AbstractTotal::collect($address);
        $this->_isTaxAffected = false;
        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $address->setAppliedTaxesReset(true);
        $address->setAppliedTaxes(array());

        $this->_store = $address->getQuote()->getStore();
        foreach ($items as $item) {
            if ($item->getParentItemId()) {
                continue;
            }
            $this->_resetItemData($item);
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $this->_resetItemData($child);
                    $this->_process($address, $child);
                }
                $this->_recalculateParent($item);
            } else {
                $this->_process($address, $item);
            }
        }

        if ($this->_isTaxAffected) {
            $address->unsSubtotalInclTax();
            $address->unsBaseSubtotalInclTax();
        }

        return $this;
    }

    /**
     * Calculate item fixed tax and prepare information for discount and recular taxation
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  void|$this
     */
    protected function _process(\Magento\Sales\Model\Quote\Address $address, $item)
    {
        if (!$this->_weeeHelper->isEnabled($this->_store)) {
            return $this;
        }

        $attributes = $this->_weeeHelper->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $this->_store->getWebsiteId()
        );

        $applied = array();
        $productTaxes = array();

        $totalValue = 0;
        $baseTotalValue = 0;
        $totalRowValue = 0;
        $baseTotalRowValue = 0;

        foreach ($attributes as $key => $attribute) {
            $baseValue      = $attribute->getAmount();
            $value          = $this->_store->convertPrice($baseValue);
            $rowValue       = $value * $item->getTotalQty();
            $baseRowValue   = $baseValue * $item->getTotalQty();
            $title          = $attribute->getName();

            $totalValue += $value;
            $baseTotalValue += $baseValue;
            $totalRowValue += $rowValue;
            $baseTotalRowValue += $baseRowValue;

            $productTaxes[] = array(
                'title' => $title,
                'base_amount' => $baseValue,
                'amount' => $value,
                'row_amount' => $rowValue,
                'base_row_amount' => $baseRowValue,
                /**
                 * Tax value can't be presented as include/exclude tax
                 */
                'base_amount_incl_tax' => $baseValue,
                'amount_incl_tax' => $value,
                'row_amount_incl_tax' => $rowValue,
                'base_row_amount_incl_tax' => $baseRowValue
            );

            $applied[] = array(
                'id'        => $attribute->getCode(),
                'percent'   => null,
                'hidden'    => $this->_weeeHelper->includeInSubtotal($this->_store),
                'rates'     => array(array(
                    'base_real_amount'=> $baseRowValue,
                    'base_amount'   => $baseRowValue,
                    'amount'        => $rowValue,
                    'code'          => $attribute->getCode(),
                    'title'         => $title,
                    'percent'       => null,
                    'position'      => 1,
                    'priority'      => -1000 + $key,
                ))
            );
        }

        $item->setWeeeTaxAppliedAmount($totalValue)
            ->setBaseWeeeTaxAppliedAmount($baseTotalValue)
            ->setWeeeTaxAppliedRowAmount($totalRowValue)
            ->setBaseWeeeTaxAppliedRowAmnt($baseTotalRowValue);

        $this->_processTaxSettings(
            $item,
            $totalValue,
            $baseTotalValue,
            $totalRowValue,
            $baseTotalRowValue
        )->_processTotalAmount(
            $address,
            $totalRowValue,
            $baseTotalRowValue
        );

        $this->_weeeHelper->setApplied($item, array_merge($this->_weeeHelper->getApplied($item), $productTaxes));
        if ($applied) {
            $this->_saveAppliedTaxes(
                $address,
                $applied,
                $item->getWeeeTaxAppliedAmount(),
                $item->getBaseWeeeTaxAppliedAmount(),
                null
            );
        }
    }

    /**
     * Check if discount should be applied to weee and add weee to discounted price
     *
     * @deprecated 
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param   float $value
     * @param   float $baseValue
     * @return  $this
     */
    protected function _processDiscountSettings($item, $value, $baseValue)
    {
        if ($this->_weeeHelper->isDiscounted($this->_store)) {
            $this->_weeeHelper->addItemDiscountPrices($item, $baseValue, $value);
        }
        return $this;
    }

    /**
     * Add extra amount which should be taxable by regular tax
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param   float $value
     * @param   float $baseValue
     * @param   float $rowValue
     * @param   float $baseRowValue
     * @return  $this
     */
    protected function _processTaxSettings($item, $value, $baseValue, $rowValue, $baseRowValue)
    {
        if ($rowValue) {
            $this->_isTaxAffected = true;
            $item->unsRowTotalInclTax()
                ->unsBaseRowTotalInclTax()
                ->unsPriceInclTax()
                ->unsBasePriceInclTax();
        }
        if ($this->_weeeHelper->isTaxable($this->_store) && $rowValue) {
            if (!$this->_weeeHelper->includeInSubtotal($this->_store)) {
                $item->setExtraTaxableAmount($value)
                    ->setBaseExtraTaxableAmount($baseValue)
                    ->setExtraRowTaxableAmount($rowValue)
                    ->setBaseExtraRowTaxableAmount($baseRowValue);
            }
        }
        return $this;
    }

    /**
     * Proces row amount based on FPT total amount configuration setting
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   float $rowValue
     * @param   float $baseRowValue
     * @return  $this
     */
    protected function _processTotalAmount($address, $rowValue, $baseRowValue)
    {
        if ($this->_weeeHelper->includeInSubtotal($this->_store)) {
            $address->addTotalAmount('subtotal', $rowValue);
            $address->addBaseTotalAmount('subtotal', $baseRowValue);
            $this->_isTaxAffected = true;
        } else {
            $address->setExtraTaxAmount($address->getExtraTaxAmount() + $rowValue);
            $address->setBaseExtraTaxAmount($address->getBaseExtraTaxAmount() + $baseRowValue);
        }
        return $this;
    }

    /**
     * Recalculate parent item amounts based on children results
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  void
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _recalculateParent(\Magento\Sales\Model\Quote\Item\AbstractItem $item)
    {
    }

    /**
     * Reset information about FPT for shopping cart item
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  void
     */
    protected function _resetItemData($item)
    {
        $this->_weeeHelper->setApplied($item, array());

        $item->setBaseWeeeTaxDisposition(0);
        $item->setWeeeTaxDisposition(0);

        $item->setBaseWeeeTaxRowDisposition(0);
        $item->setWeeeTaxRowDisposition(0);

        $item->setBaseWeeeTaxAppliedAmount(0);
        $item->setBaseWeeeTaxAppliedRowAmnt(0);

        $item->setWeeeTaxAppliedAmount(0);
        $item->setWeeeTaxAppliedRowAmount(0);
    }

    /**
     * Fetch FPT data to address object for display in totals block
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Sales\Model\Quote\Address $address)
    {
        return $this;
    }

    /**
     * Process model configuration array.
     * This method can be used for changing totals collect sort order
     *
     * @param   array $config
     * @param   Store $store
     * @return  array
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function processConfigArray($config, $store)
    {
        return $config;
    }

    /**
     * No aggregated label for fixed product tax
     *
     * TODO: fix
     * @return string
     */
    public function getLabel()
    {
        return '';
    }
}
