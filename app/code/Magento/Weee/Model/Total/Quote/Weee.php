<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Total\Quote;

use Magento\Store\Model\Store;

class Weee extends \Magento\Tax\Model\Sales\Total\Quote\Tax
{
    /**
     * Weee module helper object
     *
     * @var \Magento\Weee\Helper\Data
     */
    protected $_weeeData;

    /**
     * SalesRule module helper object
     *
     * @var \Magento\SalesRule\Helper\Data
     */
    protected $_salesRuleData;

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
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param \Magento\SalesRule\Helper\Data $salesRuleData

     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Weee\Helper\Data $weeeData,
        \Magento\SalesRule\Helper\Data $salesRuleData
    ) {
        $this->_weeeData = $weeeData;
        $this->_salesRuleData = $salesRuleData;
        parent::__construct($taxData, $calculation, $taxConfig);
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
            $this->_processItem($item, $address);
            if ($item->isChildrenCalculated()){
                $this->_recalculateParent($item);
            }
        }
        if ($this->_isTaxAffected) {
            $address->unsSubtotalInclTax();
            $address->unsBaseSubtotalInclTax();
        }

        return $this;
    }

    /**
     * Calculate item fixed tax and prepare information for discount and regular taxation
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  array
     */
    protected function _process(\Magento\Sales\Model\Quote\Address $address, $item)
    {
        if (!$this->_weeeData->isEnabled($this->_store)) {
            return $this;
        }

        $attributes = $this->_weeeData->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $this->_store->getWebsiteId()
        );

        $taxData['applied'] = array();
        $taxData['product_taxes'] = array();

        $valuesData['total'] = 0;
        $valuesData['base_total'] = 0;
        $valuesData['total_row'] = 0;
        $valuesData['base_total_row'] = 0;

        foreach ($attributes as $k => $attribute) {
            $baseValue = $attribute->getAmount();
            $value = $this->_store->convertPrice($baseValue);
            $rowValue = $value * $item->getTotalQty();
            $baseRowValue = $baseValue * $item->getTotalQty();
            $title = $attribute->getName();

            $valuesData['total'] += $value;
            $valuesData['base_total'] += $baseValue;
            $valuesData['total_row'] += $rowValue;
            $valuesData['base_total_row'] += $baseRowValue;

            $taxData['product_taxes'][] = array(
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

            $taxData['applied'][] = array(
                'id' => $attribute->getCode(),
                'percent' => null,
                'hidden' => $this->_weeeData->includeInSubtotal($this->_store),
                'rates' => array(
                    array(
                        'base_real_amount' => $baseRowValue,
                        'base_amount' => $baseRowValue,
                        'amount' => $rowValue,
                        'code' => $attribute->getCode(),
                        'title' => $title,
                        'percent' => null,
                        'position' => 1,
                        'priority' => -1000 + $k
                    )
                )
            );

        }

        return array('values' => $valuesData, 'tax' => $taxData, 'address' => $address, 'item' => $item);
    }

    /**
     * Prepare item data for processing.
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     */
    protected function _processItem($item, $address){
        $this->_resetItemDataIfHasParent($item);
        if ($item->getHasChildren() && $item->isChildrenCalculated()) {
            foreach ($item->getChildren() as $child) {
                $this->_resetItemData($child);
                $processData = $this->_process($address, $child);
                $this->_setTax($processData);
            }
        } else {
            $processData = $this->_process($address, $item);
            $this->_setTax($processData);
        }

    }

    /**
     * Reset item data if it have parent
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     */
    protected function _resetItemDataIfHasParent($item)
    {
        if (!$item->getParentItemId()) {
            $this->_resetItemData($item);
        }

    }

    /**
     * Set tax to item, process tax with total amount and discount settings
     *
     * @param array
     */
    protected function _setTax($processData)
    {
        $values  = $processData['values'];
        $tax     = $processData['tax'];
        $item    = $processData['item'];
        $address = $processData['address'];

        $item->setWeeeTaxAppliedAmount(
            $values['total']
        )->setBaseWeeeTaxAppliedAmount(
                $values['base_total']
            )->setWeeeTaxAppliedRowAmount(
                $values['total_row']
            )->setBaseWeeeTaxAppliedRowAmnt(
                $values['base_total_row']
            );

        $this->_processTaxSettings(
            $item,
            $values['total'],
            $values['base_total'],
            $values['total_row'],
            $values['base_total_row']
        )->_processTotalAmount(
                $address,
                $values['total_row'],
                $values['base_total_row']
        )->_processDiscountSettings(
                $item,
                $values['total'],
                $values['base_total']
            );

        $this->_weeeData->setApplied($item, array_merge($this->_weeeData->getApplied($item), $tax['product_taxes']));
        if ($tax['applied']) {
            $this->_saveAppliedTaxes(
                $address,
                $tax['applied'],
                $item->getWeeeTaxAppliedAmount(),
                $item->getBaseWeeeTaxAppliedAmount(),
                null
            );
        }
    }

    /**
     * Set applied taxes to item data.
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param string
     * @param string
     * @param string
     * @param string
     */
    protected function _setAppliedTaxes($item, $totalValue, $baseTotalValue, $totalRowValue, $baseTotalRowValue) {
        $item->setWeeeTaxAppliedAmount($totalValue)
            ->setBaseWeeeTaxAppliedAmount($baseTotalValue)
            ->setWeeeTaxAppliedRowAmount($totalRowValue)
            ->setBaseWeeeTaxAppliedRowAmnt($baseTotalRowValue);
    }

    /**
     * Check if discount should be applied to weee and add weee to discounted price
     *
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @param   float $value
     * @param   float $baseValue
     * @return  $this
     */
    protected function _processDiscountSettings($item, $value, $baseValue)
    {
        if ($this->_weeeData->isDiscounted($this->_store)) {
            $this->_salesRuleData->addItemDiscountPrices($item, $baseValue, $value);
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
        if ($this->_weeeData->isTaxable($this->_store) && $rowValue) {
            if (!$this->_config->priceIncludesTax($this->_store)) {
                $item->setExtraTaxableAmount($value)
                    ->setBaseExtraTaxableAmount($baseValue)
                    ->setExtraRowTaxableAmount($rowValue)
                    ->setBaseExtraRowTaxableAmount($baseRowValue);
            }
            $item->unsRowTotalInclTax()->unsBaseRowTotalInclTax()->unsPriceInclTax()->unsBasePriceInclTax();
            $this->_isTaxAffected = true;
        }
        return $this;
    }

    /**
     * Process row amount based on FPT total amount configuration setting
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   float $rowValue
     * @param   float $baseRowValue
     * @return  $this
     */
    protected function _processTotalAmount($address, $rowValue, $baseRowValue)
    {
        if ($this->_weeeData->includeInSubtotal($this->_store)) {
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
        $this->_weeeData->setApplied($item, array());

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
