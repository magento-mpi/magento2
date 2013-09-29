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
    /**
     * @var array
     */
    protected $_stateStatuses = array(
        \Magento\Sales\Model\Order::STATE_NEW,
        \Magento\Sales\Model\Order::STATE_PROCESSING,
        \Magento\Sales\Model\Order::STATE_COMPLETE,
        \Magento\Sales\Model\Order::STATE_CLOSED,
        \Magento\Sales\Model\Order::STATE_CANCELED,
        \Magento\Sales\Model\Order::STATE_HOLDED,
    );

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     */
    public function __construct(\Magento\Sales\Model\Order\Config $orderConfig)
    {
        $this->_orderConfig = $orderConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = $this->_orderConfig->getStateStatuses($this->_stateStatuses);
        } else {
            $statuses = $this->_orderConfig->getStatuses();
        }
        $options = array();
        $options[] = array(
           'value' => '',
           'label' => __('-- Please Select --')
        );
        foreach ($statuses as $code => $label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        return $options;
    }
}
