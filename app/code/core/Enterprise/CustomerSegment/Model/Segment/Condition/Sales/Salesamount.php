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
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Salesamount
    extends Enterprise_CustomerSegment_Model_Segment_Condition_Sales_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_sales_salesamount');
        $this->setValue(null);
    }

    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_customersegment')->__('%s Sales Amount %s %s while %s of these Conditions match:',
                $this->getAttributeElementHtml(), $this->getOperatorElementHtml(), $this->getValueElementHtml(),
                $this->getAggregatorElement()->getHtml())
            . $this->getRemoveLinkHtml();
    }

    protected function _prepareConditionsSql($customer, $isRoot)
    {
        $select = $this->getResource()->createSelect();

        if ($this->getAttribute() == 'total') {
            $result = "IF (SUM(order.base_grand_total) {$this->_getSqlOperator()} {$this->getValue()}, 1, 0)";
        } else {
            $result = "IF (SUM(order.base_grand_total)/COUNT(order.entity_id) {$this->_getSqlOperator()} {$this->getValue()}, 1, 0)";
        }

        $select->from(array('order' => $this->getResource()->getTable('sales/order')), array(new Zend_Db_Expr($result)));
        $select->where($this->_createCustomerFilter($customer, 'order.customer_id', $isRoot));

        return $select;
    }
}
