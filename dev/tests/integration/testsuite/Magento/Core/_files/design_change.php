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

$storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
    ->getAnyStoreView()->getId();
/** @var $change \Magento\Core\Model\Design */
$change = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Design');
$change->setStoreId($storeId)
    ->setDesign('magento_blank')
    ->setDateFrom('2001-01-01 01:01:01')
    ->save(); // creating with predefined ID doesn't work for some reason
