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

class Api_GiftCard_CustomerTest extends Magento_Test_Webservice
{
    /**
     * Test giftcard customer info by code
     *
     * @magentoDataFixture Api/GiftCard/_fixture/code_pool.php
     * @magentoDataFixture Api/GiftCard/_fixture/giftcard_account.php
     *
     * @return void
     */
    public function testInfo()
    {
        $giftcardAccount = self::getFixture('giftcard_account');
        $balance = $giftcardAccount->getData('balance');
        $dateExpires = $giftcardAccount->getData('date_expires');
        $code = $giftcardAccount->getData('code');

        $info = $this->call('giftcard_customer.info', array('code' => $code));
        $this->assertEquals($balance, $info['balance']);
        $this->assertEquals($dateExpires, $info['expire_date']);
    }

    /**
     * Test redeem amount present on gift card to Store Credit.
     *
     * @magentoDataFixture Api/GiftCard/_fixture/customer.php
     * @magentoDataFixture Api/GiftCard/_fixture/code_pool.php
     * @magentoDataFixture Api/GiftCard/_fixture/giftcard_account.php
     *
     * @return void
     */
    public function testRedeem()
    {
        /** @var $giftcardAccount Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $giftcardAccount = self::getFixture('giftcard_account');
        $code = $giftcardAccount->getCode();

        //Fixture customer id
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = self::getFixture('giftcard/customer');
        $customerId = $customer->getId();

        $storeId = 1;

        $result = $this->call(
            'giftcard_customer.redeem', array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
        $this->assertTrue($result);

        //Test giftcard redeemed to customer balance
        $customerBalance = new Enterprise_CustomerBalance_Model_Balance();
        $customerBalance->setCustomerId($customer->getId());
        $customerBalance->loadByCustomer();
        $this->assertEquals($giftcardAccount->getBalance(), $customerBalance->getAmount());

        //Test giftcard already redeemed
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call(
            'giftcard_customer.redeem', array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
    }

    /**
     * Test info throw exception with incorrect data
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testIncorrectDataInfoException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_customer.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_info);
        $this->call('giftcard_customer.info', $invalidData);
    }

    /**
     * Test redeem throw exception with incorrect data
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testIncorrectDataRedeemException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_customer.xml');
        $invalidData = self::simpleXmlToArray($fixture->invalid_redeem);
        $this->call('giftcard_customer.redeem', $invalidData);
    }
}
