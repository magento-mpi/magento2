<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $website Magento_Core_Model_Website */
$website = Mage::getModel('Magento_Core_Model_Website');
$website->setData(array(
    'code' => 'test',
    'name' => 'Test Website',
    'default_group_id' => '1',
    'is_default' => '0'
));
$website->save();

$key = 'Magento_ScheduledImportExport_Model_Website';
Mage::unregister($key);
Mage::register($key, $website);
