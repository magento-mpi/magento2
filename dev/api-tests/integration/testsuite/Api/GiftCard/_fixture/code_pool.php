<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$pool = Mage::getResourceModel('Enterprise_GiftCardAccount_Model_Resource_Pool');

do {
    $code = 'test-code-' . mt_rand(10, 9999);
} while ($pool->exists($code));
$pool->saveCode($code);

Magento_Test_Webservice::setFixture('giftcardaccount_pool_code', $code,
    Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
