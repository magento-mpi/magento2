<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Adjustments extends Mage_Adminhtml_Block_Template
{
    protected $_source;
    /**
     * Initialize creditmemo agjustment totals
     *
     * @return Mage_Tax_Block_Sales_Order_Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_source  = $parent->getSource();
        $total = new Varien_Object(array(
            'code'      => 'agjustments',
            'block_name'=> $this->getNameInLayout()
        ));
        $parent->removeTotal('shipping');
        $parent->removeTotal('adjustment_positive');
        $parent->removeTotal('adjustment_negative');
        $parent->addTotal($total);
        return $this;
    }

    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Get credit memo shipping amount depend on configuration settings
     * @return float
     */
    public function getShippingAmount()
    {
        $config = Mage::getSingleton('Mage_Tax_Model_Config');
        $source = $this->getSource();
        if ($config->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $shipping = $source->getBaseShippingInclTax();
        } else {
            $shipping = $source->getBaseShippingAmount();
        }
        return Mage::app()->getStore()->roundPrice($shipping) * 1;
    }

    /**
     * Get label for shipping total based on configuration settings
     * @return string
     */
    public function getShippingLabel()
    {
        $config = Mage::getSingleton('Mage_Tax_Model_Config');
        $source = $this->getSource();
        if ($config->displaySalesShippingInclTax($source->getOrder()->getStoreId())) {
            $label = __('Refund Shipping (Incl. Tax)');
        } elseif ($config->displaySalesShippingBoth($source->getOrder()->getStoreId())) {
            $label = __('Refund Shipping (Excl. Tax)');
        } else {
            $label = __('Refund Shipping');
        }
        return $label;
    }
}
