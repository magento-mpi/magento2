<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order Statuses source model
 */
namespace Magento\Sales\Model\Config\Source\Order;

class Status implements \Magento\Core\Model\Option\ArrayInterface
{
    // set null to enable all possible
    protected $_stateStatuses = array(
        \Magento\Sales\Model\Order::STATE_NEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING,
        \Magento\Sales\Model\Order::STATE_COMPLETE,
        \Magento\Sales\Model\Order::STATE_CLOSED,
        \Magento\Sales\Model\Order::STATE_CANCELED,
        \Magento\Sales\Model\Order::STATE_HOLDED,
    );

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = \Mage::getSingleton('Magento\Sales\Model\Order\Config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = \Mage::getSingleton('Magento\Sales\Model\Order\Config')->getStatuses();
        }
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => __('-- Please Select --')
            );
        foreach ($statuses as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;
    }
}
