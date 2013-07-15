<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Orders conditions options group
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Sales
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * @param Mage_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Mage_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Sales');
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
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Order Address')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Salesamount',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Sales Amount')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Ordersnumber',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Number of Orders')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Purchasedquantity',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Purchased Quantity')),
             ),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Sales')
        );
    }
}
