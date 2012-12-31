<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftCard_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test giftcard customer info by code
     *
     * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/code_pool.php
     * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/giftcard_account.php
     *
     * @return void
     */
    public function testInfo()
    {
        $giftcardAccount = self::getFixture('giftcard_account');
        $balance = $giftcardAccount->getData('balance');
        $dateExpires = $giftcardAccount->getData('date_expires');
        $code = $giftcardAccount->getData('code');

        $info = $this->call('giftcardCustomerInfo', array('code' => $code));
        $this->assertEquals($balance, $info['balance']);
        $this->assertEquals($dateExpires, $info['expire_date']);
    }

    /**
     * Test redeem amount present on gift card to Store Credit.
     *
     * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/customer.php
     * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/code_pool.php
     * @magentoDataFixture Api/Enterprise/GiftCard/_fixture/giftcard_account.php
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
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
        $this->assertTrue($result);

        //Test giftcard redeemed to customer balance
        $customerBalance = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
        $customerBalance->setCustomerId($customer->getId());
        $customerBalance->loadByCustomer();
        $this->assertEquals($giftcardAccount->getBalance(), $customerBalance->getAmount());

        //Test giftcard already redeemed
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call(
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
    }

    /**
     * Test info throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataInfoException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_customer.xml');
        $invalidData = Magento_Test_Helper_Api::simpleXmlToObject($fixture->invalid_info);
        $this->call('giftcardCustomerInfo', $invalidData);
    }

    /**
     * Test redeem throw exception with incorrect data
     *
     * @expectedException SoapFault
     * @return void
     */
    public function testIncorrectDataRedeemException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/xml/giftcard_customer.xml');
        $invalidData = Magento_Test_Helper_Api::simpleXmlToObject($fixture->invalid_redeem);
        $this->call('giftcardCustomerRedeem', $invalidData);
    }
}
