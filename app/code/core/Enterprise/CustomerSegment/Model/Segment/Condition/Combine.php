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
 * Segment conditions container
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Combine
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    /**
     * Intialize model
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Combine');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
        public function getNewChildSelectOptions()
    {
        $conditions = array(
            array( // subconditions combo
                'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Combine',
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions Combination')),

            array( // customer address combo
                'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Customer_Address',
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Customer Address')),

            // customer attribute group
            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Customer')->getNewChildSelectOptions(),

            // shopping cart group
            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Shoppingcart')->getNewChildSelectOptions(),

            array('value' => array(
                    array( // product list combo
                        'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_List',
                        'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Product List')),
                    array( // product history combo
                        'value' => 'Enterprise_CustomerSegment_Model_Segment_Condition_Product_Combine_History',
                        'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Product History')),
                ),
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Products'),
            ),

            // sales group
            Mage::getModel('Enterprise_CustomerSegment_Model_Segment_Condition_Sales')->getNewChildSelectOptions(),
        );

        $conditions = array_merge_recursive(parent::getNewChildSelectOptions(), $conditions);
        return $conditions;
    }

    /**
     * Prepare base condition select which related with current condition combine
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    protected function _prepareConditionsSql($customer, $website)
    {
        $select = parent::_prepareConditionsSql($customer, $website);
        $select->limit(1);
        return $select;
    }
}
