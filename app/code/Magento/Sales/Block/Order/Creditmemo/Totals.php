<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Block\Order\Creditmemo;

class Totals extends \Magento\Sales\Block\Order\Totals
{
    protected $_creditmemo = null;

    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (\Mage::registry('current_creditmemo')) {
                $this->_creditmemo = \Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }
        return $this->_creditmemo;
    }

    public function setCreditmemo($creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Initialize order totals array
     *
     * @return \Magento\Sales\Block\Order\Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal('base_grandtotal');
        if ((float) $this->getSource()->getAdjustmentPositive()) {
            $total = new \Magento\Object(array(
                'code'  => 'adjustment_positive',
                'value' => $this->getSource()->getAdjustmentPositive(),
                'label' => __('Adjustment Refund')
            ));
            $this->addTotal($total);
        }
        if ((float) $this->getSource()->getAdjustmentNegative()) {
            $total = new \Magento\Object(array(
                'code'  => 'adjustment_negative',
                'value' => $this->getSource()->getAdjustmentNegative(),
                'label' => __('Adjustment Fee')
            ));
            $this->addTotal($total);
        }
        /**
        <?php if ($this->getCanDisplayTotalPaid()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo __('Total Paid') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalPaid()) ?></strong></td>
        </tr>
        <?php endif; ?>
        <?php if ($this->getCanDisplayTotalRefunded()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo __('Total Refunded') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalRefunded()) ?></strong></td>
        </tr>
        <?php endif; ?>
        <?php if ($this->getCanDisplayTotalDue()): ?>
        <tr>
            <td colspan="6" class="a-right"><strong><?php echo __('Total Due') ?></strong></td>
            <td class="last a-right"><strong><?php echo $_order->formatPrice($_creditmemo->getTotalDue()) ?></strong></td>
        </tr>
        <?php endif; ?>
         */
        return $this;
    }


}
