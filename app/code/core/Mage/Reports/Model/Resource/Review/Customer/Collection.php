<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Report Customers Review collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Resource_Review_Customer_Collection extends Mage_Review_Model_Resource_Review_Collection
{
    /**
     * Join customers
     *
     * @return Mage_Reports_Model_Resource_Review_Customer_Collection
     */
    public function joinCustomers()
    {
        $adapter  = $this->getSelect()->getAdapter();
        $customer = Mage::getResourceSingleton('customer/customer');
        //TODO: add full name logic
        $firstnameAttr   = $customer->getAttribute('firstname');
        $firstnameAttrId = $firstnameAttr->getAttributeId();
        $firstnameTable  = $firstnameAttr->getBackend()->getTable();

        $attrCondition = array('table_customer_firstname.entity_id = detail.customer_id');
        if ($firstnameAttr->getBackend()->isStatic()) {
            $firstnameField = 'firstname';
        } else {
            $firstnameField = 'value';
            $attrCondition[] = $adapter->quoteInto('table_customer_firstname.attribute_id = ?', $firstnameAttrId);
        }

        $this->getSelect()->joinInner(
            array('table_customer_firstname' => $firstnameTable),
            implode(' AND ', $attrCondition),
            array());

        $lastnameAttr   = $customer->getAttribute('lastname');
        $lastnameAttrId = $lastnameAttr->getAttributeId();
        $lastnameTable  = $lastnameAttr->getBackend()->getTable();

        $attrCondition = array('table_customer_lastname.entity_id = detail.customer_id');
        if ($lastnameAttr->getBackend()->isStatic()) {
            $lastnameField = 'lastname';
        } else {
            $lastnameField = 'value';
            $attrCondition[] = $adapter->quoteInto('table_customer_lastname.attribute_id = ?', $lastnameAttrId);
        }

        $customerName = $this->getConnection()->getConcatSql(array(
            "table_customer_firstname.{$firstnameField}",
            "table_customer_lastname.{$lastnameField}"
        ), ' ');
        $this->getSelect()->joinInner(
            array('table_customer_lastname' => $lastnameTable),
             implode(' AND ', $attrCondition),
            array())
            ->columns(array(
                        'customer_name' => $customerName,
                        'review_cnt' => "COUNT(main_table.review_id)"))
            ->group('detail.customer_id');

        return $this;
    }

    /**
     * Get select count sql
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->_select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns("COUNT(DISTINCT detail.customer_id)");

        $sql = $countSelect->__toString();

        return $sql;
    }
}
