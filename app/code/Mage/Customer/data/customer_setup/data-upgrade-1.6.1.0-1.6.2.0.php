<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;

$disableAGCAttribute = Mage::getSingleton('Magento_Eav_Model_Config')
    ->getAttribute('customer', 'disable_auto_group_change');
$disableAGCAttribute->setData('used_in_forms', array(
    'adminhtml_customer'
));
$disableAGCAttribute->save();

$vatAttribute = Mage::getSingleton('Magento_Eav_Model_Config')->getAttribute('customer_address', 'vat_id');
$vatAttribute->setData('used_in_forms', array(
     'adminhtml_customer_address',
     'customer_address_edit',
     'customer_register_address'
));
$vatAttribute->save();
