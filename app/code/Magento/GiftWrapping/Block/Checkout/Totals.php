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
namespace Magento\GiftWrapping\Block\Checkout;

class Totals extends \Magento\Checkout\Block\Total\DefaultTotal
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
        $totals = \Mage::helper('Magento\GiftWrapping\Helper\Data')->getTotals($total);
        foreach ($totals as $total) {
            $values[$total['label']] = $total['value'];
        }
        return $values;
    }
}
