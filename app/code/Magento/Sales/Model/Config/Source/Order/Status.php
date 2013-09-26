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
class Magento_Sales_Model_Config_Source_Order_Status implements Magento_Core_Model_Option_ArrayInterface
{
    // set null to enable all possible
    /**
     * @var array
     */
    protected $_stateStatuses = array(
        Magento_Sales_Model_Order::STATE_NEW,
        Magento_Sales_Model_Order::STATE_PROCESSING,
        Magento_Sales_Model_Order::STATE_COMPLETE,
        Magento_Sales_Model_Order::STATE_CLOSED,
        Magento_Sales_Model_Order::STATE_CANCELED,
        Magento_Sales_Model_Order::STATE_HOLDED,
    );

    /**
     * @var Magento_Sales_Model_Order_Config
     */
    protected $_orderConfig;

    /**
     * @param Magento_Sales_Model_Order_Config $orderConfig
     */
    public function __construct(Magento_Sales_Model_Order_Config $orderConfig)
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
