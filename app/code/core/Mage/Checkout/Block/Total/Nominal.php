<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal total rendered
 *
 * Each item is rendered as separate total with its details
 */
class Mage_Checkout_Block_Total_Nominal extends Mage_Checkout_Block_Total_Default
{
    /**
     * Custom template
     *
     * @var string
     */
    protected $_template = 'total/nominal.phtml';

    /**
     * Getter for a quote item name
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return string
     */
    public function getItemName(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getName();
    }

    /**
     * Getter for a quote item row total
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return float
     */
    public function getItemRowTotal(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalRowTotal();
    }

    /**
     * Getter for nominal total item details
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return array
     */
    public function getTotalItemDetails(Mage_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalTotalDetails();
    }

    /**
     * Getter for details row label
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getItemDetailsRowLabel(Varien_Object $row)
    {
        return $row->getLabel();
    }

    /**
     * Getter for details row amount
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getItemDetailsRowAmount(Varien_Object $row)
    {
        return $row->getAmount();
    }

    /**
     * Getter for details row compounded state
     *
     * @param Varien_Object $row
     * @return bool
     */
    public function getItemDetailsRowIsCompounded(Varien_Object $row)
    {
        return $row->getIsCompounded();
    }

    /**
     * Format an amount without container
     *
     * @param float $amount
     * @return string
     */
    public function formatPrice($amount)
    {
        return $this->_store->formatPrice($amount, false);
    }

    /**
     * Import total data into the block, if there are items
     *
     * @return string
     */
    protected function _toHtml()
    {
        $total = $this->getTotal();
        $items = $total->getItems();
        if ($items) {
            foreach ($total->getData() as $key => $value) {
                $this->setData("total_{$key}", $value);
            }
            return parent::_toHtml();
        }
        return '';
    }
}
