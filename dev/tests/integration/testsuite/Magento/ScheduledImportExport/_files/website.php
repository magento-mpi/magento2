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

/** @var $website \Magento\Store\Model\Website */
$website = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Store\Model\Website');
$website->setData(array(
    'code' => 'test',
    'name' => 'Test Website',
    'default_group_id' => '1',
    'is_default' => '0'
));
$website->save();
