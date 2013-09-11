<?php
/**
 * Gift card account for customer API model test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GiftCardAccount_Model_Api_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test giftcard customer info by code
     *
     * @magentoDataFixture Magento/GiftCardAccount/_files/code_pool.php
     * @magentoDataFixture Magento/GiftCardAccount/_files/giftcardaccount.php
     */
    public function testInfo()
    {
        $balance = 9.99;
        $dateExpires = date('Y-m-d');
        $code = 'giftcardaccount_fixture';

        $info = Magento_TestFramework_Helper_Api::call($this, 'giftcardCustomerInfo', array('code' => $code));
        $this->assertEquals($balance, $info['balance']);
        $this->assertEquals($dateExpires, date('Y-m-d', strtotime($info['expire_date'])));
    }

    /**
     * Test redeem amount present on gift card to Store Credit.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/GiftCardAccount/_files/code_pool.php
     * @magentoDataFixture Magento/GiftCardAccount/_files/giftcardaccount.php
     */
    public function testRedeem()
    {
        $code = 'giftcardaccount_fixture';

        //Fixture customer id
        $customerId = 1;
        $storeId = 1;

        $result = Magento_TestFramework_Helper_Api::call($this,
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
        $this->assertTrue($result);

        //Test giftcard redeemed to customer balance
        $customerBalance = Mage::getModel('\Magento\CustomerBalance\Model\Balance');
        $customerBalance->setCustomerId($customerId);
        $customerBalance->loadByCustomer();
        $this->assertEquals(9.99, $customerBalance->getAmount());

        //Test giftcard already redeemed
        Magento_TestFramework_Helper_Api::callWithException($this,
            'giftcardCustomerRedeem',
            array('code' => $code, 'customerId' => $customerId, 'storeId' => $storeId)
        );
    }

    /**
     * Test info throw exception with incorrect data
     */
    public function testIncorrectDataInfoException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../../_files/fixture/giftcard_customer.xml');
        $invalidData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->invalidInfo);
        Magento_TestFramework_Helper_Api::callWithException($this, 'giftcardCustomerInfo', (array)$invalidData);
    }

    /**
     * Test redeem throw exception with incorrect data
     */
    public function testIncorrectDataRedeemException()
    {
        $fixture = simplexml_load_file(dirname(__FILE__) . '/../../_files/fixture/giftcard_customer.xml');
        $invalidData = Magento_TestFramework_Helper_Api::simpleXmlToArray($fixture->invalidRedeem);
        Magento_TestFramework_Helper_Api::callWithException($this, 'giftcardCustomerRedeem', (array)$invalidData);
    }
}
