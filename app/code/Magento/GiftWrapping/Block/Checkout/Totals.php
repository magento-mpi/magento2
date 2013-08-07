<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping total block for checkout
 *
 */
class Magento_GiftWrapping_Block_Checkout_Totals extends Magento_Checkout_Block_Total_Default
{
    /**
     * Template file path
     *
     * @var string
     */
    protected $_template = 'checkout/totals.phtml';

    /**
     * Return information for showing
     *
     * @return array
     */
    public function getValues(){
        $values = array();
        $total = $this->getTotal();
        $totals = Mage::helper('Magento_GiftWrapping_Helper_Data')->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }
        return $values;
    }
}
