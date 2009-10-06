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
 * Order address condition
 */
class Enterprise_CustomerSegment_Model_Segment_Condition_Order_Address
    extends Enterprise_CustomerSegment_Model_Condition_Combine_Abstract
{
    protected $_inputType = 'select';

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_order_address');
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
     * Get List of available selections inside this combine
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_order_address_combine')
            ->getNewChildSelectOptions();
    }

    /**
     * Get html of order address combine
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('If Order Addresses match %s of these Conditions:',
                $this->getAggregatorElement()->getHtml()) . $this->getRemoveLinkHtml();
    }

    /**
     * Order address combine doesn't have declared value. We use "1" for it
     *
     * @return int
     */
    public function getValue()
    {
        return 1;
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
        $resource = $this->getResource();
        $select = $resource->createSelect();

        $addressEntityType = Mage::getSingleton('eav/config')->getEntityType('order_address');
        $orderEntityType = Mage::getSingleton('eav/config')->getEntityType('order');

        $addressTable = $resource->getTable($addressEntityType->getEntityTable());
        $orderTable = $resource->getTable($orderEntityType->getEntityTable());

        $orderAddressEntityId = $addressEntityType->getId();
        $addressTypeAttribute = Mage::getSingleton('eav/config')->getAttribute('order_address', 'address_type');

        $select->from(array('order_address' => $addressTable), array(new Zend_Db_Expr(1)));
        $select->where('order_address.entity_type_id = ?', $orderAddressEntityId);

        $orderJoinConditions = 'order_address.parent_id = order_address_order.entity_id';
        $select->joinInner(array('order_address_order' => $orderTable), $orderJoinConditions, array());

        $select->joinInner(
            array('order_address_type' => $addressTypeAttribute->getBackendTable()),
            'order_address.entity_id = order_address_type.entity_id',
            array()
        );
        $select->where('order_address_type.attribute_id = ?', $addressTypeAttribute->getId());
        $select->where($this->_createCustomerFilter($customer, 'order_address_order.customer_id'));
        $select->limit(1);

        return $select;
    }

    /**
     * Order address is joined to base query. We are applying address type condition as subfilter for main query
     *
     * @return array
     */
    protected function _getSubfilterMap()
    {
        return array(
            'order_address_type' => 'order_address_type.value',
        );
    }
}
