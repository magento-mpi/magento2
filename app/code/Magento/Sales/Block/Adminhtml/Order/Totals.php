<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Block\Adminhtml\Order;

/**
 * Adminhtml order totals block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals//\Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new \Magento\Framework\Object(
            [
                'code' => 'paid',
                'strong' => true,
                'value' => $this->getSource()->getTotalPaid(),
                'base_value' => $this->getSource()->getBaseTotalPaid(),
                'label' => __('Total Paid'),
                'area' => 'footer',
            ]
        );
        $this->_totals['refunded'] = new \Magento\Framework\Object(
            [
                'code' => 'refunded',
                'strong' => true,
                'value' => $this->getSource()->getTotalRefunded(),
                'base_value' => $this->getSource()->getBaseTotalRefunded(),
                'label' => __('Total Refunded'),
                'area' => 'footer',
            ]
        );
        $this->_totals['due'] = new \Magento\Framework\Object(
            [
                'code' => 'due',
                'strong' => true,
                'value' => $this->getSource()->getTotalDue(),
                'base_value' => $this->getSource()->getBaseTotalDue(),
                'label' => __('Total Due'),
                'area' => 'footer',
            ]
        );
        return $this;
    }
}
