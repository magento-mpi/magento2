<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$accountFixture = simplexml_load_file(dirname(__FILE__) . '/xml/giftcard_account.xml');
$accountCreateData = Magento_Test_Helper_Api::simpleXmlToObject($accountFixture->create);

$giftcardAccount = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
$giftcardAccount->setData($accountCreateData);
$giftcardAccount->save();

PHPUnit_Framework_TestCase::setFixture(
    'giftcard_account',
    $giftcardAccount,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);
