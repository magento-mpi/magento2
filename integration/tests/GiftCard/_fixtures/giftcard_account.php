<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$accountFixture = simplexml_load_file(dirname(__FILE__) . '/xml/giftcard_account.xml');
$accountCreateData = Magento_Test_Webservice::simpleXmlToArray($accountFixture->create);

$giftcardAccount = new Enterprise_GiftCardAccount_Model_Giftcardaccount();
$giftcardAccount->setData($accountCreateData);
$giftcardAccount->save();

Magento_Test_Webservice::setFixture('giftcard_account', $giftcardAccount);
