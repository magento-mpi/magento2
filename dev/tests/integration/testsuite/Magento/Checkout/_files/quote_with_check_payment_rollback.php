<?php
/**
 * Rollback for quote_with_check_payment.php fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->unregister('quote');
