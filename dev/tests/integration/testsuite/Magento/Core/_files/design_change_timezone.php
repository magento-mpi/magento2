<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$designChanges = array(
    array('store' => 'default', 'design' => 'default_yesterday_design', 'date' => '-1 day'),
    array('store' => 'default', 'design' => 'default_today_design',     'date' => 'now'),
    array('store' => 'default', 'design' => 'default_tomorrow_design',  'date' => '+1 day'),
    array('store' => 'admin',   'design' => 'admin_yesterday_design',   'date' => '-1 day'),
    array('store' => 'admin',   'design' => 'admin_today_design',       'date' => 'now'),
    array('store' => 'admin',   'design' => 'admin_tomorrow_design',    'date' => '+1 day'),
);
foreach ($designChanges as $designChangeData) {
    $storeId = Mage::app()->getStore($designChangeData['store'])->getId();
    $change = Mage::getModel('Magento\Core\Model\Design');
    $change->setStoreId($storeId)
        ->setDesign($designChangeData['design'])
        ->setDateFrom($designChangeData['date'])
        ->setDateTo($designChangeData['date'])
        ->save()
    ;
}
