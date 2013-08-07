<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Orders conditions options group
 */
class Magento_CustomerSegment_Model_Segment_Condition_Sales
    extends Magento_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_CustomerSegment_Model_Segment_Condition_Sales');
        $this->setValue(null);
    }

    /**
     * Get condition "selectors"
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => array(
                array( // order address combo
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Order_Address',
                    'label' => Mage::helper('Magento_CustomerSegment_Helper_Data')->__('Order Address')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount',
                    'label' => Mage::helper('Magento_CustomerSegment_Helper_Data')->__('Sales Amount')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Ordersnumber',
                    'label' => Mage::helper('Magento_CustomerSegment_Helper_Data')->__('Number of Orders')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Purchasedquantity',
                    'label' => Mage::helper('Magento_CustomerSegment_Helper_Data')->__('Purchased Quantity')),
             ),
            'label' => Mage::helper('Magento_CustomerSegment_Helper_Data')->__('Sales')
        );
    }
}
