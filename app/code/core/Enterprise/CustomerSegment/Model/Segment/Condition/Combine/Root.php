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


class Enterprise_CustomerSegment_Model_Segment_Condition_Combine_Root
    extends Enterprise_CustomerSegment_Model_Segment_Condition_Combine
{
    public function getValidationEvent()
    {
        return 'customersegment_test_event';
    }

    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_customersegment/segment_condition_combine_root');
    }

    protected function _createCustomerFilter($customer, $fieldName)
    {
        if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        } else if ($customer instanceof Zend_Db_Select) {
            $customer = new Zend_Db_Expr($customer);
        }

        return $this->getResource()->quoteInto("{$fieldName} IN (?)", $customer);
    }

    protected function _prepareConditionsSql($customer, $website) {
        $select = $this->getResource()->createSelect();

        $table = array('root' => $this->getResource()->getTable('customer/entity'));

        $select->from($table, array(new Zend_Db_Expr(1)));
        $select->where($this->_createCustomerFilter($customer, 'entity_id'));

        return $select;
    }
}
