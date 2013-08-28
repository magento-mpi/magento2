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
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
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
                    'label' => __('Order Address')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Salesamount',
                    'label' => __('Sales Amount')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Ordersnumber',
                    'label' => __('Number of Orders')),
                array(
                    'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Purchasedquantity',
                    'label' => __('Purchased Quantity')),
             ),
            'label' => __('Sales')
        );
    }
}
