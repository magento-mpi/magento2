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
     * @param Magento_CustomerSegment_Model_Resource_Segment $resourceSegment
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerSegment_Model_Resource_Segment $resourceSegment,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($resourceSegment, $context, $data);
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
                    'label' => __('Order Address')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Salesamount',
                    'label' => __('Sales Amount')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Ordersnumber',
                    'label' => __('Number of Orders')),
                array(
                    'value' => 'Magento_CustomerSegment_Model_Segment_Condition_Sales_Purchasedquantity',
                    'label' => __('Purchased Quantity')),
             ),
            'label' => __('Sales')
        );
    }
}
