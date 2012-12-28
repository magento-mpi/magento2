<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$accountFixture = simplexml_load_file(dirname(__FILE__) . '/xml/giftcard_account.xml');
$accountCreateData = Magento_Test_TestCase_ApiAbstract::simpleXmlToObject($accountFixture->create);

$giftcardAccount = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount');
$giftcardAccount->setData($accountCreateData);
$giftcardAccount->save();

Magento_Test_TestCase_ApiAbstract::setFixture(
    'giftcard_account',
    $giftcardAccount,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);
