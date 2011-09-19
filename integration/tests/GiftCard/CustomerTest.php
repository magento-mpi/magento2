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

/**
 * @magentoDataFixture GiftCard/_fixtures/code_pool.php
 * @magentoDataFixture GiftCard/_fixtures/giftcard_account.php
 */
class GiftCard_CustomerTest extends Magento_Test_Webservice
{
    /**
     * Test giftcard customer info by code
     *
     * @return void
     */
    public function testInfo()
    {
        $giftcardAccount = self::getFixture('giftcard_account');
        $balance = $giftcardAccount->getData('balance');
        $dateExpires = $giftcardAccount->getData('date_expires');
        $code = $giftcardAccount->getData('code');
        
        $info = $this->call('giftcard_customer.info', array($code));
        $this->assertEquals($balance, $info['balance']);
        $this->assertEquals($dateExpires, $info['expire_date']);
    }
    
    /**
     * Test redeem amount present on gift card to Store Credit.
     *
     * @magentoDataFixture _fixtures/customer.php
     */
    public function testRedeem()
    {
        $giftcardAccount = self::getFixture('giftcard_account');
        $code = $giftcardAccount->getData('code');
        //Fixture customer id
        $customerId = 10001;
        //Default website has id 1
        $websiteId = 1;

        $result = $this->call('giftcard_customer.redeem', array($code, $customerId, $websiteId));
        $this->assertTrue($result);
    }

    /**
     * Test info throw exception with incorrect data
     *
     * @return void
     */
    public function testIncorrectDataInfoException()
    {
        $this->setExpectedException('Exception');

        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_customer.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_info);
        $this->call('giftcard_customer.info', $invalidData);
    }

    /**
     * Test redeem throw exception with incorrect data
     *
     * @return void
     */
    public function testIncorrectDataRedeemException()
    {
        $this->setExpectedException('Exception');

        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/giftcard_customer.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_redeem);
        $this->call('giftcard_customer.redeem', $invalidData);
    }
}
