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
 * Gift wrapping total block for admin checkout
 *
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Order\Create;

class Totals extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals
{
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
