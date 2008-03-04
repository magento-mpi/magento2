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
 * @category   Mage
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Coupons Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */

class Mage_Reports_Model_Mysql4_Coupons_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{

    protected $_from = '';
    protected $_to = '';

    public function __construct()
    {
        parent::__construct();
        return $this;
    }

    public function setDateRange($from, $to)
    {
        $this->_from = $from;
        $this->_to = $to;
        $this->_reset();
        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $this->joinOrders($this->_from, $this->_to, $storeIds);
        return $this;
    }

    public function joinOrders($from, $to, $storeIds = array())
    {
        $order = Mage::getResourceSingleton('sales/order');
        /* @var $order Mage_Sales_Model_Entity_Order */

        $storeFilter = '';
        $storeIds = array_values($storeIds);
        if (count($storeIds) > 0) {
            if ($storeIds[0] != '') {
                $storeFilter = " AND s2.store_id IN (".implode(',', $storeIds).")";
            }
        }

        $attr = $order->getAttribute('coupon_code');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $couponCodeAttrId = $attr->getAttributeId();
        $couponCodeTableName = $attr->getBackend()->getTable();
        $couponCodeFieldName = $attr->getBackend()->isStatic() ? 'coupon_code' : 'value';

        $this->getSelect()
            ->joinLeft(array("s1" => $couponCodeTableName),
                "s1.{$couponCodeFieldName}=main_table.coupon_code AND s1.attribute_id={$couponCodeAttrId}", array())
            ->joinLeft(array("s2" => $order->getEntityTable()),
                "s2.{$order->getEntityIdField()}=s1.{$order->getEntityIdField()}
                AND s2.created_at BETWEEN '{$from}' AND '{$to}'".$storeFilter, array());

        $attr = $order->getAttribute('discount_amount');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $discountTotalAttrId = $attr->getAttributeId();
        $discountTotalTableName = $attr->getBackend()->getTable();
        $discountTotalFieldName = $attr->getBackend()->isStatic() ? 'discount_amount' : 'value';

        $this->getSelect()
            ->joinLeft(array("s3" => $discountTotalTableName),
                "s3.{$order->getEntityIdField()}=s2.{$order->getEntityIdField()}
                AND s3.attribute_id={$discountTotalAttrId}", array());

        $attr = $order->getAttribute('subtotal');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $subTotalAttrId = $attr->getAttributeId();
        $subTotalTableName = $attr->getBackend()->getTable();
        $subTotalFieldName = $attr->getBackend()->isStatic() ? 'subtotal' : 'value';

        $this->getSelect()
            ->joinLeft(array("s4" => $subTotalTableName),
                "s4.{$order->getEntityIdField()}=s2.{$order->getEntityIdField()}
                AND s4.attribute_id={$subTotalAttrId}", array())
            ->from("", array("uses" => "COUNT(s2.entity_id)",
                "discount" => "SUM(s3.{$discountTotalFieldName})",
                "total" => "SUM(s4.{$subTotalFieldName})"))
            ->group('main_table.rule_id')
            ->order('uses desc')
            ->having('uses > 0');

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->from("", "count(DISTINCT main_table.rule_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }
}