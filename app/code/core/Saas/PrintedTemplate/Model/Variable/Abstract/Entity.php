<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for templtate variable
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Abstract_Entity extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Key of value property for caching wraped taxes
     *
     * @var string
     */
    const TAXES_GROUPED_BY_PERCENT_CACHE_KEY = '__taxes_grouped_by_percent_variables';

    /**
     * Base identifier
     *
     * @var string
     */
    protected $_type;

    /**
     * Cache for current locale
     *
     * @var Zend_Locale|null
     */
    protected $_locale;

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatCurrency($value)
    {
        return (null !== $value) ? $this->_value->getOrder()->formatPriceTxt($value) : '';
    }

    /**
     * Formats currency using order formater
     *
     * @param float
     * @return string
     */
    public function formatBaseCurrency($value)
    {
        return $this->_value->getOrder()->formatBasePrice($value);
    }

    /**
     * Get entity items
     *
     * @return array
     */
    public function getItems()
    {
        $items = array();
        foreach ($this->_value->getAllItems() as $item) {
            $item->setTaxRates($this->_getItemTaxes($item));
            if ($item->getOrderItem()->getParentItemId()) {
                continue;
            }
            $items[] = Mage::getModel('Saas_PrintedTemplate_Model_Variable_Item_' . $this->_type, $item);
        }

        return $items;
    }

    /**
     * Retrive item taxes array
     *
     * @param Mage_Sales_Model_Order_Invoice_Item|Mage_Sales_Model_Order_Creditmemo_Item|Mage_Sales_Model_Order_Shipment_Item $item
     * @return array Of Saas_PrintedTemplate_Model_Tax_Order_Item
     */
    protected function _getItemTaxes($item)
    {
        $itemsTaxes = $this->_getTaxCollections();
        if (is_null($itemsTaxes) || !isset($itemsTaxes['items_taxes'])) {
            return array();
        }

        $itemsTaxes = $itemsTaxes['items_taxes'];
        return $itemsTaxes->getItemsByColumnValue('item_id', $item->getOrderItemId());
    }

    /**
     * Get type property
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Load value from cache
     * Uses value as cache backend
     *
     * @param string $key
     * @return mixed
     */
    protected function _loadFromCache($key)
    {
        return $this->_value->getData($key);
    }

    /**
     * Save data to cache
     * Uses value as cache backend
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function _saveToCache($key, $value)
    {
        $this->_value->setData($key, $value);
    }

    /**
     * Returns array of item tax variables
     *
     * Tax info is aggregated by percent and each item has next properties:
     *  - amount          Total of product prices that tax was applied
     *  - base_amount     Same but in base currency
     *  - percent         Rate fo tax, does not take into account way fo tax appying
     *  - tax_amount      Total amount of tax with certain percent
     *  - base_tax_amount Same but in base currency
     *
     * @return array
     */
    public function getTaxesGroupedByPercent()
    {
        $items = array();
        foreach ($this->_getTaxCollections() as $taxes) {
            foreach ($taxes as $tax) {
                $items[(string)$tax->getPercent()][] = $tax;
            }
        }
        foreach ($items as &$item) {
            $summary = $this->_summarizeTax($item);
            $item = Mage::getModel('Saas_PrintedTemplate_Model_Variable_Tax', $summary);
            $item->setOrder($summary->getOrder());
        }

        return $items;
    }

    /**
     * Returns taxes grouped by compound ID
     *
     * @return array Array of saas_printedtemplate/variable_tax
     */
    public function getTaxesGroupedByCompoundId()
    {
        // Build coumpound ID for each item, summirize, real percent and set tax
        $itemInfo = array();
        foreach ($this->_getTaxCollections() as $taxes) {
            $prev = null;
            foreach ($taxes as $tax) {
                $id = $tax->getItemId();
                if (!isset($itemInfo[$id])) {
                    $itemInfo[$id] = new Varien_Object(array(
                        'compound_id'      => Mage::getModel('Saas_PrintedTemplate_Model_Tax_CompoundId'),
                        'tax_amount'       => $tax->getTaxAmount(),
                        'row_total'        => $tax->getRowTotal(),
                        'discount'         => $tax->getDiscountAmount(),
                        'tax_real_percent' => 0,
                    ));
                    if ($tax->getIsTaxAfterDiscount()) {
                        $itemInfo[$id]->row_total -= $tax->getDiscountAmount();
                    }
                }

                $itemInfo[$id]->tax_real_percent += $tax->getRealPercent() / 100.;

                // add percentages to compund ID object
                if ($prev && $prev->getItemId() == $tax->getItemId() && $prev->getPriority() == $tax->getPriority()) {
                    $itemInfo[$id]->compound_id->addAnd($tax->getPercent());
                } else {
                    $itemInfo[$id]->compound_id->addAfter($tax->getPercent());
                }
                $prev = $tax;
            }
        }

        // Group tax info by compound ID
        $taxes = array();
        foreach ($itemInfo as $id => $info) {
            $cid = (string)$info->compound_id;
            if (!isset($taxes[$cid])) {
                $taxes[$cid] = new Varien_Object(array(
                    'compound_id'  => $info->compound_id,
                    'tax_amount'   => 0,
                    'total_amount' => 0,
                    'row_total_without_discount'  => 0,
                    'tax_amount_without_discount' => 0,
                ));
            }
            $taxes[$cid]->total_amount += $info->row_total;
            $taxes[$cid]->tax_amount += $info->tax_amount;

            // calculate row total and tax amount without discount taxes, it is a special values for French invoice
            $discountExcTax = $info->discount / (1 + $info->tax_real_percent);
            $taxes[$cid]->total_amount_without_discount += $info->row_total - $discountExcTax;
            $taxes[$cid]->tax_amount_without_discount += $info->tax_amount - $discountExcTax * $info->tax_real_percent;
        }

        // Wrap with variable
        foreach ($taxes as &$tax) {
            $tax = Mage::getModel('Saas_PrintedTemplate_Model_Variable_Tax', $tax)
                ->setOrder($this->_value->getOrder());
        }

        return $taxes;
    }

    /**
     * Summarize amount, base_amount, tax_amount and base_tax amount
     *
     * @param array $taxes Collections of Varien_Objects
     * @return Varien_Object With properties amount, base_amount, tax_amount and base_tax amount
     */
    private function _summarizeTax(array $taxes)
    {
        $summary = new Varien_Object;
        foreach ($taxes as $tax) {
            if ($tax instanceof Varien_Object) {
                $summary->total_amount += $tax->total_amount;
                $summary->base_total_amount += $tax->base_total_amount;
                $summary->tax_amount += $tax->tax_amount;
                $summary->base_tax_amount += $tax->base_tax_amount;
                $summary->percent = $tax->percent;
                $summary->order_id = $tax->order_id;
                $summary->order = $tax->order;
            }
        }

        return $summary;
    }

    /**
     * Returns items' and shipping tax collection with applied filters
     *
     * @return array array($itemsTaxes, $shippingTax)
     */
    protected function _getTaxCollections()
    {
        if ($taxCollections = $this->_loadFromCache(self::TAXES_GROUPED_BY_PERCENT_CACHE_KEY)) {
            return $taxCollections;
        }

        $itemsTaxes = Mage::getResourceModel('Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection');
        $shippingTaxes = Mage::getResourceModel('Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection');

        if ($this->_value instanceof Mage_Sales_Model_Order) {
            $itemsTaxes->addFilterByOrder($this->_value);
            $shippingTaxes->addFilterByOrder($this->_value);
        } else if ($this->_value instanceof Mage_Sales_Model_Order_Invoice) {
            $itemsTaxes->addFilterByInvoice($this->_value);
            $shippingTaxes->addFilterByInvoice($this->_value);
        } else if ($this->_value instanceof Mage_Sales_Model_Order_Creditmemo) {
            $itemsTaxes->addFilterByCreditmemo($this->_value);
            $shippingTaxes->addFilterByCreditmemo($this->_value);
        }

        $taxCollections = array('items_taxes' => $itemsTaxes, 'shipping_taxes' => $shippingTaxes);
        $this->_saveToCache(self::TAXES_GROUPED_BY_PERCENT_CACHE_KEY, $taxCollections);

        return $taxCollections;
    }
}
