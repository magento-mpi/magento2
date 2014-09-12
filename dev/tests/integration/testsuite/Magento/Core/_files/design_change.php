<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\StoreManagerInterface'
)->getDefaultStoreView()->getId();
/** @var $change \Magento\Core\Model\Design */
$change = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Core\Model\Design');
$change->setStoreId($storeId)->setDesign('Magento/plushe')->setDateFrom('2001-01-01 01:01:01')->save();
