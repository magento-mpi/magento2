<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model\Total\Quote;

use Magento\Store\Model\Store;
use Magento\Tax\Model\Calculation;
use Magento\Sales\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class Weee extends AbstractTotal
{
    /**
     * Constant for weee item code prefix
     */
    const ITEM_CODE_WEEE_PREFIX = 'weee';
    /**
     * Constant for weee item type
     */
    const ITEM_TYPE = 'weee';

    /**
     * @var \Magento\Weee\Helper\Data
     */
    protected $weeeData;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Static counter
     *
     * @var int
     */
    protected static $counter = 0;

    /**
     * Array to keep track of weee taxable item code to quote item
     *
     * @var array
     */
    protected $weeeCodeToItemMap;

    /**
     * @param \Magento\Weee\Helper\Data $weeeData
     */
    public function __construct(
        \Magento\Weee\Helper\Data $weeeData
    ) {
        $this->weeeData = $weeeData;
        $this->setCode('weee');
        $this->weeeCodeToItemMap = [];
    }

    /**
     * Collect Weee amounts for the quote / order
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  $this
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        AbstractTotal::collect($address);
        $this->_store = $address->getQuote()->getStore();
        if (!$this->weeeData->isEnabled($this->_store)) {
            return $this;
        }

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

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

        $address->setWeeeCodeToItemMap($this->weeeCodeToItemMap);
        return $this;
    }

    /**
     * Calculate item fixed tax and prepare information for discount and regular taxation
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return  void|$this
     */
    protected function _process(\Magento\Sales\Model\Quote\Address $address, $item)
    {
        $attributes = $this->weeeData->getProductWeeeAttributes(
            $item->getProduct(),
            $address,
            $address->getQuote()->getBillingAddress(),
            $this->_store->getWebsiteId()
        );

        $productTaxes = array();

        $totalValueInclTax = 0;
        $baseTotalValueInclTax = 0;
        $totalRowValueInclTax = 0;
        $baseTotalRowValueInclTax = 0;

        $totalValueExclTax = 0;
        $baseTotalValueExclTax = 0;
        $totalRowValueExclTax = 0;
        $baseTotalRowValueExclTax = 0;

        $associatedTaxables = $item->getAssociatedTaxables();
        if (!$associatedTaxables) {
            $associatedTaxables = [];
        }
        foreach ($attributes as $key => $attribute) {
            $title          = $attribute->getName();

            $baseValueExclTax = $baseValueInclTax = $attribute->getAmount();
            $valueExclTax = $valueInclTax = $this->_store->roundPrice($this->_store->convertPrice($baseValueExclTax));

            $rowValueInclTax = $rowValueExclTax = $this->_store->roundPrice($valueInclTax * $item->getTotalQty());
            $baseRowValueInclTax = $this->_store->roundPrice($baseValueInclTax * $item->getTotalQty());
            $baseRowValueExclTax = $baseRowValueInclTax;

            $totalValueInclTax += $valueInclTax;
            $baseTotalValueInclTax += $baseValueInclTax;
            $totalRowValueInclTax += $rowValueInclTax;
            $baseTotalRowValueInclTax += $baseRowValueInclTax;


            $totalValueExclTax += $valueExclTax;
            $baseTotalValueExclTax += $baseValueExclTax;
            $totalRowValueExclTax += $rowValueExclTax;
            $baseTotalRowValueExclTax += $baseRowValueExclTax;

            $productTaxes[] = array(
                'title' => $title,
                'base_amount' => $baseValueExclTax,
                'amount' => $valueExclTax,
                'row_amount' => $rowValueExclTax,
                'base_row_amount' => $baseRowValueExclTax,
                'base_amount_incl_tax' => $baseValueInclTax,
                'amount_incl_tax' => $valueInclTax,
                'row_amount_incl_tax' => $rowValueInclTax,
                'base_row_amount_incl_tax' => $baseRowValueInclTax,
            );

            if ($this->weeeData->isTaxable($this->_store)) {
                $itemTaxCalculationId = $item->getTaxCalculationItemId();
                $weeeItemCode = self::ITEM_CODE_WEEE_PREFIX . $this->getNextIncrement();
                $weeeItemCode .= '-' . $title;
                $associatedTaxables[] = [
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TYPE => self::ITEM_TYPE,
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_CODE => $weeeItemCode,
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_UNIT_PRICE => $valueExclTax,
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_BASE_UNIT_PRICE => $baseValueExclTax,
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_QUANTITY => $item->getQty(),
                    CommonTaxCollector::KEY_ASSOCIATED_TAXABLE_TAX_CLASS_ID => $item->getProduct()->getTaxClassId(),
                ];
                $this->weeeCodeToItemMap[$weeeItemCode] = $item;
            }
        }
        $item->setAssociatedTaxables($associatedTaxables);

        $item->setWeeeTaxAppliedAmount($totalValueExclTax)
            ->setBaseWeeeTaxAppliedAmount($baseTotalValueExclTax)
            ->setWeeeTaxAppliedRowAmount($totalRowValueExclTax)
            ->setBaseWeeeTaxAppliedRowAmnt($baseTotalRowValueExclTax);

        $item->setWeeeTaxAppliedAmountInclTax($totalValueInclTax)
            ->setBaseWeeeTaxAppliedAmountInclTax($baseTotalValueInclTax)
            ->setWeeeTaxAppliedRowAmountInclTax($totalRowValueInclTax)
            ->setBaseWeeeTaxAppliedRowAmntInclTax($baseTotalRowValueInclTax);

        $this->processTotalAmount(
            $address,
            $totalRowValueExclTax,
            $baseTotalRowValueExclTax,
            $totalRowValueInclTax,
            $baseTotalRowValueInclTax
        );

        $this->weeeData->setApplied($item, array_merge($this->weeeData->getApplied($item), $productTaxes));
    }

    /**
     * Process row amount based on FPT total amount configuration setting
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @param   float $rowValueExclTax
     * @param   float $baseRowValueExclTax
     * @param   float $rowValueInclTax
     * @param   float $baseRowValueInclTax
     * @return  $this
     */
    protected function processTotalAmount($address, $rowValueExclTax, $baseRowValueExclTax, $rowValueInclTax, $baseRowValueInclTax)
    {
        if (!$this->weeeData->isTaxable($this->_store)) {
            //otherwise, defer to weee_tax collector to update subtotal and tax
            if ($this->weeeData->includeInSubtotal($this->_store)) {
                $address->addTotalAmount('subtotal', $this->_store->roundPrice($rowValueExclTax));
                $address->addBaseTotalAmount('subtotal', $this->_store->roundPrice($baseRowValueExclTax));
            } else {
                $address->addTotalAmount('weee', $rowValueExclTax);
                $address->addBaseTotalAmount('weee', $baseRowValueExclTax);
            }
        }
        
        //This value is used to calculate shipping cost, it will be overridden by tax collector
        $address->setSubtotalInclTax($address->getSubtotalInclTax() + $this->_store->roundPrice($rowValueInclTax));
        $address->setBaseSubtotalInclTax(
            $address->getBaseSubtotalInclTax() + $this->_store->roundPrice($baseRowValueInclTax)
        );
        return $this;
    }

    /**
     * Increment and return static counter. This function is intended to be used to generate temporary
     * id for an item.
     *
     * @return int
     */
    protected function getNextIncrement()
    {
        return ++self::$counter;
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
        $this->weeeData->setApplied($item, array());

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
     * Delegate this to WeeeTax collector
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
