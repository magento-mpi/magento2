<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Nominal total rendered
 *
 * Each item is rendered as separate total with its details
 */
class Magento_Checkout_Block_Total_Nominal extends Magento_Checkout_Block_Total_Default
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
     * @param Magento_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return string
     */
    public function getItemName(Magento_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getName();
    }

    /**
     * Getter for a quote item row total
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return float
     */
    public function getItemRowTotal(Magento_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalRowTotal();
    }

    /**
     * Getter for nominal total item details
     *
     * @param Magento_Sales_Model_Quote_Item_Abstract $quoteItem
     * @return array
     */
    public function getTotalItemDetails(Magento_Sales_Model_Quote_Item_Abstract $quoteItem)
    {
        return $quoteItem->getNominalTotalDetails();
    }

    /**
     * Getter for details row label
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getItemDetailsRowLabel(Magento_Object $row)
    {
        return $row->getLabel();
    }

    /**
     * Getter for details row amount
     *
     * @param Magento_Object $row
     * @return string
     */
    public function getItemDetailsRowAmount(Magento_Object $row)
    {
        return $row->getAmount();
    }

    /**
     * Getter for details row compounded state
     *
     * @param Magento_Object $row
     * @return bool
     */
    public function getItemDetailsRowIsCompounded(Magento_Object $row)
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
