<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml order creditmemo totals block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Creditmemo;

class Totals extends \Magento\Adminhtml\Block\Sales\Totals
{
    protected $_creditmemo;

    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (\Mage::registry('current_creditmemo')) {
                $this->_creditmemo = \Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock() && $this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }
        return $this->_creditmemo;
    }

    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Initialize creditmemo totals array
     *
     * @return \Magento\Sales\Block\Order\Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->addTotal(new \Magento\Object(array(
            'code'      => 'adjustment_positive',
            'value'     => $this->getSource()->getAdjustmentPositive(),
            'base_value'=> $this->getSource()->getBaseAdjustmentPositive(),
            'label'     => __('Adjustment Refund')
        )));
        $this->addTotal(new \Magento\Object(array(
            'code'      => 'adjustment_negative',
            'value'     => $this->getSource()->getAdjustmentNegative(),
            'base_value'=> $this->getSource()->getBaseAdjustmentNegative(),
            'label'     => __('Adjustment Fee')
        )));
        return $this;
    }
}
