<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
$pool = Mage::getResourceModel('\Magento\GiftCardAccount\Model\Resource\Pool');

do {
    $code = 'test-code-' . mt_rand(10, 9999);
} while ($pool->exists($code));
$pool->saveCode($code);

Magento_GiftCardAccount_Model_ApiTest::$code = $code;
