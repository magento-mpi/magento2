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
 * Adminhtml order totals block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order;

class Totals extends \Magento\Adminhtml\Block\Sales\Totals//\Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    /**
     * Initialize order totals array
     *
     * @return \Magento\Sales\Block\Order\Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->_totals['paid'] = new \Magento\Object(array(
            'code'      => 'paid',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalPaid(),
            'base_value'=> $this->getSource()->getBaseTotalPaid(),
            'label'     => __('Total Paid'),
            'area'      => 'footer'
        ));
        $this->_totals['refunded'] = new \Magento\Object(array(
            'code'      => 'refunded',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalRefunded(),
            'base_value'=> $this->getSource()->getBaseTotalRefunded(),
            'label'     => __('Total Refunded'),
            'area'      => 'footer'
        ));
        $this->_totals['due'] = new \Magento\Object(array(
            'code'      => 'due',
            'strong'    => true,
            'value'     => $this->getSource()->getTotalDue(),
            'base_value'=> $this->getSource()->getBaseTotalDue(),
            'label'     => __('Total Due'),
            'area'      => 'footer'
        ));
        return $this;
    }
}
