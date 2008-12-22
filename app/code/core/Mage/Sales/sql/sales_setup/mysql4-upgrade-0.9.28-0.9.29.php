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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->getConnection()->addColumn($this->getTable('sales/order'), 'discount_refunded', 'decimal(12,4) default NULL AFTER `subtotal_canceled`');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'discount_canceled', 'decimal(12,4) default NULL AFTER `discount_refunded`');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'base_discount_refunded', 'decimal(12,4) default NULL AFTER `base_subtotal_canceled`');
$installer->getConnection()->addColumn($this->getTable('sales/order'), 'base_discount_canceled', 'decimal(12,4) default NULL AFTER `base_discount_refunded`');

$installer->addAttribute('order', 'discount_refunded', array('type'=>'static'));
$installer->addAttribute('order', 'discount_canceled', array('type'=>'static'));
$installer->addAttribute('order', 'base_discount_refunded', array('type'=>'static'));
$installer->addAttribute('order', 'base_discount_canceled', array('type'=>'static'));

$entityTypeId = $installer->getEntityTypeId('creditmemo');
$orderAttribute = Mage::getModel('eav/config')->getAttribute($entityTypeId, 'order_id');
$discountAttribute = Mage::getModel('eav/config')->getAttribute($entityTypeId, 'discount_amount');
$baseDiscountAttribute = Mage::getModel('eav/config')->getAttribute($entityTypeId, 'base_discount_amount');

$select = $installer->getConnection()->select()
    ->from(
        array('e_int' => $orderAttribute->getBackend()->getTable()),
        array('order_id' => 'e_int.value')
    )
    ->joinInner(
        array('e_decimal' => $discountAttribute->getBackend()->getTable()),
        $installer->getConnection()->quoteInto(
            'e_int.entity_id=e_decimal.entity_id AND e_decimal.attribute_id=?',
            $discountAttribute->getAttributeId()
        ),
        array('order_discount' => 'SUM(e_decimal.value)')
    )
    ->joinInner(
        array('e_decimal_base' => $baseDiscountAttribute->getBackend()->getTable()),
        $installer->getConnection()->quoteInto(
            'e_int.entity_id=e_decimal_base.entity_id AND e_decimal_base.attribute_id=?',
            $baseDiscountAttribute->getAttributeId()
        ),
        array('order_base_discount' => 'SUM(e_decimal_base.value)')
    )
    ->where($installer->getConnection()->quoteInto('e_int.entity_type_id=?', $entityTypeId))
    ->where($installer->getConnection()->quoteInto('e_int.attribute_id=?', $orderAttribute->getAttributeId()))
    ->group('e_int.value');


$ordersEntity = $installer->getEntityType('order');
$ordersTable = $installer->getTable($ordersEntity['entity_table']);

// Update discount_refunded (base_discount_refunded)
$stmt = $installer->getConnection()->query($select);
while($row = $stmt->fetch()) {
    $data = array(
        'discount_refunded' => $row['order_discount'],
        'base_discount_refunded' => $row['order_base_discount']
    );
    $installer->getConnection()->update(
        $ordersTable,
        $data,
        $installer->getConnection()->quoteInto('entity_id=?', $row['order_id'])
    );
}

// Update discount_canceled (base_discount_canceled)
$statusAttribute = Mage::getModel('eav/config')->getAttribute($ordersEntity['entity_type_id'], 'status');
$select = $installer->getConnection()->select()
    ->from(
        array('s' => $statusAttribute->getBackend()->getTable()),
        array('order_id' => 's.entity_id')
    )
    ->where('s.attribute_id=?', $statusAttribute->getAttributeId())
    ->where('s.entity_type_id=?', $statusAttribute->getEntityTypeId())
    ->where('s.value=?', Mage_Sales_Model_Order::STATE_CANCELED);

$stmt = $installer->getConnection()->query($select);
while($row = $stmt->fetch()) {
    $entityId = $row['order_id'];
    $installer->run("
        UPDATE `{$ordersTable}` SET
            `discount_canceled`=`discount_amount`-`discount_refunded`,
            `base_discount_canceled`=`base_discount_amount`-`base_discount_refunded`
        WHERE `entity_id`='{$entityId}'
    ");
}