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
 * Order address type condition
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Type
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    /**
     * Condition Input Type
     *
     * @var string
     */
    protected $_inputType = 'select';

    /**
     * Define Type and value
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Type');
        $this->setValue('shipping');
    }

    /**
     * Get array of event names where segment with such conditions combine can be matched
     *
     * @return array
     */
    public function getMatchedEvents()
    {
        return array('sales_order_save_commit_after');
    }

    /**
     * Get inherited conditions selectors
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return array(
            'value' => $this->getType(),
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Address Type')
        );
    }

    /**
     * Initialize value select options
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Type
     */
    public function loadValueOptions()
    {
        $this->setValueOption(array(
            'shipping' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Shipping'),
            'billing'  => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Billing'),
        ));
        return $this;
    }

    /**
     * Get input type for attribute value.
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Get HTML of condition string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Order Address %s a %s Address', $this->getOperatorElementHtml(), $this->getValueElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Get condition query for order address attribute
     *
     * @param $customer
     * @param $website
     * @return Varien_Db_Select
     */
    public function getConditionsSql($customer, $website)
    {
        return $this->getResource()->createConditionSql(
            'order_address.address_type',
            $this->getOperator(),
            $this->getValue()
        );
    }
}