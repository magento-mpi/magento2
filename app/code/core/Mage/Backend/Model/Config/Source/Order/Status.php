<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order Statuses source model
 */
class Mage_Backend_Model_Config_Source_Order_Status
{
    // set null to enable all possible
    protected $_stateStatuses = array(
        Mage_Sales_Model_Order::STATE_NEW,
//        Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
        Mage_Sales_Model_Order::STATE_PROCESSING,
        Mage_Sales_Model_Order::STATE_COMPLETE,
        Mage_Sales_Model_Order::STATE_CLOSED,
        Mage_Sales_Model_Order::STATE_CANCELED,
        Mage_Sales_Model_Order::STATE_HOLDED,
    );

    public function toOptionArray()
    {
        if ($this->_stateStatuses) {
            $statuses = Mage::getSingleton('Mage_Sales_Model_Order_Config')->getStateStatuses($this->_stateStatuses);
        }
        else {
            $statuses = Mage::getSingleton('Mage_Sales_Model_Order_Config')->getStatuses();
        }
        $options = array();
        $options[] = array(
               'value' => '',
               'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('-- Please Select --')
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
