<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
$pool = Mage::getResourceModel('Enterprise_GiftCardAccount_Model_Resource_Pool');

do {
    $code = 'test-code-' . mt_rand(10, 9999);
} while ($pool->exists($code));
$pool->saveCode($code);

Enterprise_GiftCardAccount_Model_ApiTest::$code = $code;
