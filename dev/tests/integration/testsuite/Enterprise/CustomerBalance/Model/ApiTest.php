<?php
/**
 * Customer balance tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Enterprise/CustomerBalance/_files/CustomerBalance.php
 */
class Enterprise_CustomerBalance_Model_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customer = null;

    /**
     * Customer without balance fixture
     *
     * @var Mage_Customer_Model_Customer
     */
    public static $customerNoBalance = null;

    /**
     * Test successful customer balance info
     */
    public function testCustomerBalanceBalance()
    {
        $customerBalanceData = simplexml_load_file(dirname(__FILE__) . '/../_files/fixture/CustomerBalance.xml');
        $data = Magento_Test_Helper_Api::simpleXmlToArray($customerBalanceData);

        $data['input']['customerId'] = self::$customer->getId();

        $result = Magento_Test_Helper_Api::call($this, 'enterpriseCustomerbalanceBalance', $data['input']);
        $this->assertEquals($data['expected']['balance'], $result, 'This balance value is not expected');
    }

    /**
     * Test customer balance info exception: balance not found
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceBalanceExceptionBalanceNotFound()
    {
        $this->setExpectedException('SoapFault', 'Balance with requested parameters is not found.');
        $params = array('customerId' => self::$customerNoBalance->getId(), 'websiteId' => 1);
        Magento_Test_Helper_Api::call($this, 'enterpriseCustomerbalanceBalance', $params);
    }

    /**
     * Test successful customer balance history
     *
     * @depends testCustomerBalanceBalance
     */
    public function testCustomerBalanceHistory()
    {
        $balanceHistory = simplexml_load_file(
            dirname(__FILE__) . '/../_files/fixture/CustomerBalanceHistory.xml'
        );
        $data = Magento_Test_Helper_Api::simpleXmlToArray($balanceHistory);

        $data['input']['customerId'] = self::$customer->getId();

        $result = Magento_Test_Helper_Api::call(
            $this,
            'enterpriseCustomerbalanceHistory',
            array($data['input']['customerId'], $data['input']['websiteId'])
        );

        $this->assertEquals(count($data['expected']['history_items']), count($result), 'History checksum fail');

        foreach ($data['expected']['history_items'] as $index => $expectedHistoryItem) {
            foreach ($expectedHistoryItem as $key => $value) {
                $this->assertEquals($value, $result[$index][$key], 'History item value fail');
            }
        }
    }

    /**
     * Test customer balance history exception: history not found
     *
     * @depends testCustomerBalanceHistory
     */
    public function testCustomerBalanceHistoryExceptionHistoryNotFound()
    {
        $this->setExpectedException('SoapFault', 'History with requested parameters is not found.');
        $params = array('customerId' => self::$customerNoBalance->getId(), 'websiteId' => 1);
        Magento_Test_Helper_Api::call($this, 'enterpriseCustomerbalanceHistory', $params);
    }
}
