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
 * Customers Report collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Dmytro Vasylenko  <dimav@varien.com>
 */

class Mage_Reports_Model_Mysql4_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected function _construct()
    {
        parent::_construct();
    }

    public function addCartInfo()
    {
        foreach ($this->getItems() as $item)
        {
            $quote = Mage::getResourceModel('sales/quote_collection')
                        ->loadByCustomerId($item->getId());

            if (is_object($quote))
            {
                $totals = $quote->getTotals();
                $item->setTotal($totals['subtotal']->getValue());
                $quote_items = Mage::getResourceModel('sales/quote_item_collection')->setQuoteFilter($quote->getId());
                $quote_items->load();
                $item->setItems($quote_items->count());
            } else {
                $item->remove();
            }

        }
        return $this;
    }

    public function addCustomerName()
    {
        $this->addAttributeToSelect('firstname')
            ->addAttributeToSelect('lastname')
            ->addExpressionAttributeToSelect('name', 'CONCAT({{firstname}}," ",{{lastname}})', array('firstname', 'lastname'));

        return $this;
    }

    public function addOrdersCount()
    {
        $customer = Mage::getResourceSingleton('customer/customer');
        /* @var $customer Mage_Catalog_Model_Entity_Product */
        $this->customerEntityId = $customer->getEntityIdField();

        $countSelect = clone $this->getSelect();
        $countSelect->reset();
        $order = Mage::getResourceSingleton('sales/order');
        /* @var $order Mage_Sales_Model_Entity_Quote */
        $attr = $order->getAttribute('customer_id');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $attrId = $attr->getAttributeId();
        $tableName = $attr->getBackend()->getTable();
        $fieldName = $attr->getBackend()->isStatic() ? 'customer_id' : 'value';

        $countSelect->from($tableName, "count(*)")
            ->where("{$tableName}.{$fieldName} = e.{$this->customerEntityId}
                        and {$tableName}.attribute_id = {$attrId}");

        $this->getSelect()
            ->from("", array("orders" => "({$countSelect})"))
            ->group("e.{$this->customerEntityId}");

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
        $countSelect->from("", "count(DISTINCT e.entity_id)");
        $sql = $countSelect->__toString();
        return $sql;
    }
}
