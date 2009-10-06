<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
/**
 * Order address type condition
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address_Type
    extends Enterprise_CustomerSegment_Model_Condition_Abstract
{
    protected $_inputType = 'select';

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_address_type');
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
            'label' => Mage::helper('enterprise_customersegment')->__('Address Type')
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
            'shipping' => Mage::helper('enterprise_customersegment')->__('Shipping'),
            'billing'  => Mage::helper('enterprise_customersegment')->__('Billing'),
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
            . Mage::helper('enterprise_customersegment')->__('Order Address %s a %s Address',
                $this->getOperatorElementHtml(), $this->getValueElement()->getHtml()) . $this->getRemoveLinkHtml();
    }

    /**
     * Get type of allowed subfilter
     *
     * @return string
     */
    public function getSubfilterType()
    {
        return 'order_address_type';
    }

    /**
     * Apply address type subfilter to parent/base condition query
     *
     * @param string $fieldName base query field name
     * @param bool $requireValid strict validation flag
     * @param $website
     * @return string
     */
    public function getSubfilterSql($fieldName, $requireValid, $website)
    {
        $operator = (($this->getOperator() == '==') == $requireValid);
        if ($operator) {
            $operator = '=';
        } else {
            $operator = '<>';
        }

        return sprintf("%s %s '%s'", $fieldName, $operator, $this->getValue());
    }
}
