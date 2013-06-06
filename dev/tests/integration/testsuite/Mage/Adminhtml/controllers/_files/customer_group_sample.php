<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Mage_Customer_Model_Group $group */
$group = Mage::getModel('Mage_Customer_Model_Group');

$groupData = array(
    'customer_group_code' => 'New Customer Group',
    'tax_class_id' => 3
);
$group->setData($groupData);
$group->save();
Mage::getObjectManager()->get('Mage_Core_Model_Registry')->register('_fixture/Mage_Customer_Model_Group', $group);
