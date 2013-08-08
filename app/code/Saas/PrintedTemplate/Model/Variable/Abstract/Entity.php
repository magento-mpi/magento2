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
     * Key of value property for caching wrapped taxes
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
     * Formats currency using order formatter
     *
     * @param float $value
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
            $items[] = $this->_getVariableItemModel(array('value' => $item));
        }

        return $items;
    }

    /**
     * Get variable item model
     *
     * @param mixed $arguments Arguments for model initialization
     * @return false|Magento_Core_Model_Abstract
     */
    protected function _getVariableItemModel($arguments)
    {
        return Mage::getModel(
            'Saas_PrintedTemplate_Model_Variable_Item_' . ucfirst($this->_type),
            $arguments
        );
    }

    /**
     * Retrive item taxes array
     *
     * @param Mage_Sales_Model_Order_(Invoice/Creditmemo/Shipping)_Item
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
            $value = $this->_summarizeTax($item);
            $item = $this->_getTaxVariableModel(array('value' => $value));
            $item->setOrder($value->getOrder());
        }

        return $items;
    }

    /**
     * Get tax variables model
     *
     * @param mixed $arguments
     *
     * @return Saas_PrintedTemplate_Model_Variable_Tax
     */
    protected function _getTaxVariableModel($arguments)
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Variable_Tax', $arguments);
    }

    /**
     * Get tax compound id model
     * @return Saas_PrintedTemplate_Model_Tax_CompoundId
     */
    protected function _getTaxCompoundIdModel()
    {
        return Mage::getModel('Saas_PrintedTemplate_Model_Tax_CompoundId');
    }

    /**
     * Returns taxes grouped by compound ID
     *
     * @return array Array of saas_printedtemplate/variable_tax
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getTaxesGroupedByCompoundId()
    {
        // Build compound ID for each item, summarize, real percent and set tax
        $itemInfo = array();
        foreach ($this->_getTaxCollections() as $taxes) {
            $prev = null;
            foreach ($taxes as $tax) {
                $id = $tax->getItemId();
                if (!isset($itemInfo[$id])) {
                    $itemInfo[$id] = new Magento_Object(array(
                        'compound_id'      => $this->_getTaxCompoundIdModel(),
                        'tax_amount'       => $tax->getTaxAmount(),
                        'row_total'        => $tax->getRowTotal(),
                        'discount'         => $tax->getDiscountAmount(),
                        'tax_real_percent' => 0,
                    ));
                    if ($tax->getIsTaxAfterDiscount()) {
                        $itemInfo[$id]->setRowTotal($itemInfo[$id]->getRowTotal() - $tax->getDiscountAmount());
                    }
                }

                $itemInfo[$id]->setTaxRealPercent($itemInfo[$id]->getTaxRealPercent() + $tax->getRealPercent() / 100.);

                // add percentages to compund ID object
                if ($prev && $prev->getItemId() == $tax->getItemId() && $prev->getPriority() == $tax->getPriority()) {
                    $itemInfo[$id]->getCompoundId()->addAnd($tax->getPercent());
                } else { //var_dump($itemInfo[$id]);die();
                    $itemInfo[$id]->getCompoundId()->addAfter($tax->getPercent());
                }
                $prev = $tax;
            }
        }

        // Group tax info by compound ID
        $taxes = array();
        foreach ($itemInfo as $id => $info) {
            $cid = (string)$info->getCompoundId();
            if (!isset($taxes[$cid])) {
                $taxes[$cid] = new Magento_Object(array(
                    'compound_id'  => $info->getCompoundId(),
                    'tax_amount'   => 0,
                    'total_amount' => 0,
                    'row_total_without_discount'  => 0,
                    'tax_amount_without_discount' => 0,
                ));
            }
            $taxes[$cid]->setTotalAmount($taxes[$cid]->getTotalAmount() + $info->getRowTotal());
            $taxes[$cid]->setTaxAmount($taxes[$cid]->getTaxAmount() + $info->getTaxAmount());

            // calculate row total and tax amount without discount taxes, it is a special values for French invoice
            $discountExcTax = $info->getDiscount() / (1 + $info->getTaxRealPercent());
            $taxes[$cid]->setTotalAmountWithoutDiscount(
                $taxes[$cid]->getTotalAmountWithoutDiscount() + $info->getRowTotal() - $discountExcTax
            );
            $taxes[$cid]->setTaxAmountWithoutDiscount(
                $taxes[$cid]->getTaxAmountWithoutDiscount() + $info->getTaxAmount()
                    - $discountExcTax * $info->getTaxRealPercent()
            );
        }


        // Wrap with variable
        foreach ($taxes as &$tax) {
            $tax = $this->_getTaxVariableModel(array('value' => $tax))
                ->setOrder($this->_value->getOrder());
        }

        return $taxes;
    }

    /**
     * Summarize amount, base_amount, tax_amount and base_tax amount
     *
     * @param array $taxes Collections of Magento_Objects
     * @return Magento_Object With properties amount, base_amount, tax_amount and base_tax amount
     */
    private function _summarizeTax(array $taxes)
    {
        $summary = new Magento_Object;
        foreach ($taxes as $tax) {
            if ($tax instanceof Magento_Object) {
                $summary->setTotalAmount($summary->getTotalAmount() + $tax->getTotalAmount());
                $summary->setBaseTotalAmount($summary->getBaseTotalAmount() + $tax->getBaseTotalAmount());
                $summary->setTaxAmount($summary->getTaxAmount() + $tax->getTaxAmount());
                $summary->setBaseTaxAmount($summary->getBaseTaxAmount() + $tax->getBaseTaxAmount());
                $summary->setPercent($tax->getPercent());
                $summary->setOrderId($tax->getOrderId());
                $summary->setOrder($tax->getOrder());
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
        $taxCollections = $this->_loadFromCache(self::TAXES_GROUPED_BY_PERCENT_CACHE_KEY);
        if ($taxCollections) {
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
