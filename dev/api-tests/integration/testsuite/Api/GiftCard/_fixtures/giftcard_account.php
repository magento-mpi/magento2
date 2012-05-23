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

$accountFixture = simplexml_load_file(dirname(__FILE__) . '/xml/giftcard_account.xml');
$accountCreateData = Magento_Test_Webservice::simpleXmlToArray($accountFixture->create);

$giftcardAccount = new Enterprise_GiftCardAccount_Model_Giftcardaccount();
$giftcardAccount->setData($accountCreateData);
$giftcardAccount->save();

Magento_Test_Webservice::setFixture('giftcard_account', $giftcardAccount,
    Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
