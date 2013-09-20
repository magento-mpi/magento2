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

$storeId = \Mage::app()->getAnyStoreView()->getId();
/** @var $change \Magento\Core\Model\Design */
$change = \Mage::getModel('Magento\Core\Model\Design');
$change->setStoreId($storeId)
    ->setDesign('magento_blank')
    ->setDateFrom('2001-01-01 01:01:01')
    ->save(); // creating with predefined ID doesn't work for some reason
